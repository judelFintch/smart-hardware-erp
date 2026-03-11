<?php

namespace App\Http\Controllers;

use App\Models\InventoryCount;
use App\Models\InventoryCountItem;
use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockLocation;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryCountController extends Controller
{
    public function create(): View
    {
        $locations = StockLocation::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('inventory_counts.create', compact('locations', 'products'));
    }

    public function store(Request $request, StockService $stockService): RedirectResponse
    {
        $items = array_values(array_filter($request->input('items', []), function ($item) {
            return !empty($item['product_id']) && $item['counted_quantity'] !== null && $item['counted_quantity'] !== '';
        }));

        $payload = $request->all();
        $payload['items'] = $items;

        $data = \Illuminate\Support\Facades\Validator::make($payload, [
            'location_id' => ['required', 'exists:stock_locations,id'],
            'counted_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.counted_quantity' => ['required', 'numeric', 'min:0'],
        ])->validate();

        $inventory = InventoryCount::create([
            'location_id' => $data['location_id'],
            'counted_at' => $data['counted_at'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        foreach ($data['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            $balance = StockBalance::where('product_id', $product->id)
                ->where('location_id', $inventory->location_id)
                ->first();

            $systemQty = (float) ($balance?->quantity ?? 0);
            $countedQty = (float) $item['counted_quantity'];
            $diff = $countedQty - $systemQty;
            $unitCost = (float) ($balance?->avg_cost_local ?? $product->avg_cost_local);
            $unitSale = (float) $product->sale_price_local;

            InventoryCountItem::create([
                'inventory_count_id' => $inventory->id,
                'product_id' => $product->id,
                'counted_quantity' => $countedQty,
                'system_quantity' => $systemQty,
                'difference' => $diff,
                'unit_cost_local' => $unitCost,
                'unit_sale_price_local' => $unitSale,
            ]);

            if ($diff !== 0.0) {
                $stockService->recordMovement([
                    'product_id' => $product->id,
                    'from_location_id' => $diff < 0 ? $inventory->location_id : null,
                    'to_location_id' => $diff > 0 ? $inventory->location_id : null,
                    'quantity' => abs($diff),
                    'unit_cost_local' => $unitCost,
                    'unit_sale_price_local' => $unitSale,
                    'type' => $diff > 0 ? 'adjustment_in' : 'adjustment_out',
                    'reference_type' => InventoryCount::class,
                    'reference_id' => $inventory->id,
                    'occurred_at' => $inventory->counted_at ?? now(),
                ]);
            }
        }

        return redirect()->route('dashboard');
    }
}
