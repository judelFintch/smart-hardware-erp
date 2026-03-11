<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockLocation;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockTransferController extends Controller
{
    public function create(): View
    {
        $products = Product::orderBy('name')->get();

        return view('stock_transfers.create', compact('products'));
    }

    public function store(Request $request, StockService $stockService): RedirectResponse
    {
        $items = array_values(array_filter($request->input('items', []), function ($item) {
            return !empty($item['product_id']) && !empty($item['quantity']);
        }));

        $payload = $request->all();
        $payload['items'] = $items;

        $data = \\Illuminate\\Support\\Facades\\Validator::make($payload, [
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
        ])->validate();

        $depot = StockLocation::where('code', 'depot')->firstOrFail();
        $magasin = StockLocation::where('code', 'magasin')->firstOrFail();

        foreach ($data['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            $balance = StockBalance::where('product_id', $product->id)
                ->where('location_id', $depot->id)
                ->first();

            $unitCost = $balance?->avg_cost_local ?? $product->avg_cost_local;

            $stockService->recordMovement([
                'product_id' => $product->id,
                'from_location_id' => $depot->id,
                'to_location_id' => $magasin->id,
                'quantity' => (float) $item['quantity'],
                'unit_cost_local' => (float) $unitCost,
                'unit_sale_price_local' => (float) $product->sale_price_local,
                'type' => 'transfer_in',
                'reference_type' => 'stock_transfer',
                'reference_id' => null,
                'occurred_at' => now(),
            ]);
        }

        return redirect()->route('dashboard');
    }
}
