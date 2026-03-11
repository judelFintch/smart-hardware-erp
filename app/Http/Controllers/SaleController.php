<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockBalance;
use App\Models\StockLocation;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(): View
    {
        $sales = Sale::with('customer')->orderByDesc('sold_at')->get();

        return view('sales.index', compact('sales'));
    }

    public function export(): StreamedResponse
    {
        $sales = Sale::with('customer')->orderByDesc('sold_at')->get();

        return response()->streamDownload(function () use ($sales) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Client', 'Type', 'Statut', 'Total', 'Payé', 'Date']);

            foreach ($sales as $sale) {
                fputcsv($handle, [
                    $sale->id,
                    $sale->customer?->name,
                    $sale->type,
                    $sale->status,
                    $sale->total_amount,
                    $sale->paid_total,
                    $sale->sold_at,
                ]);
            }

            fclose($handle);
        }, 'sales.csv', ['Content-Type' => 'text/csv']);
    }

    public function create(): View
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('sales.create', compact('customers', 'products'));
    }

    public function store(Request $request, StockService $stockService): RedirectResponse
    {
        $items = array_values(array_filter($request->input('items', []), function ($item) {
            return !empty($item['product_id']) && !empty($item['quantity']);
        }));

        $payload = $request->all();
        $payload['items'] = $items;

        $data = \Illuminate\Support\Facades\Validator::make($payload, [
            'type' => ['required', 'in:cash,credit'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'sold_at' => ['nullable', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.discount_amount' => ['nullable', 'numeric', 'min:0'],
        ])->validate();

        $sale = Sale::create([
            'customer_id' => $data['customer_id'] ?? null,
            'type' => $data['type'],
            'status' => $data['type'] === 'cash' ? 'paid' : 'open',
            'subtotal' => 0,
            'discount_total' => 0,
            'total_amount' => 0,
            'paid_total' => $data['type'] === 'cash' ? 0 : 0,
            'sold_at' => $data['sold_at'] ?? now(),
        ]);

        $magasin = StockLocation::where('code', 'magasin')->firstOrFail();

        $subtotal = 0;
        $discountTotal = 0;

        foreach ($data['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            $quantity = (float) $item['quantity'];
            $unitPrice = (float) $product->sale_price_local;
            $discount = (float) ($item['discount_amount'] ?? 0);

            $lineTotal = ($unitPrice * $quantity) - $discount;

            $balance = StockBalance::where('product_id', $product->id)
                ->where('location_id', $magasin->id)
                ->first();

            $unitCost = $balance?->avg_cost_local ?? $product->avg_cost_local;

            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $product->id,
                'location_id' => $magasin->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'unit_cost_local' => $unitCost,
                'discount_amount' => $discount,
                'line_total' => $lineTotal,
            ]);

            $stockService->recordMovement([
                'product_id' => $product->id,
                'from_location_id' => $magasin->id,
                'to_location_id' => null,
                'quantity' => $quantity,
                'unit_cost_local' => $unitCost,
                'unit_sale_price_local' => $unitPrice,
                'type' => 'sale_out',
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
                'occurred_at' => $sale->sold_at,
            ]);

            $subtotal += $unitPrice * $quantity;
            $discountTotal += $discount;
        }

        $total = $subtotal - $discountTotal;

        $update = [
            'subtotal' => $subtotal,
            'discount_total' => $discountTotal,
            'total_amount' => $total,
        ];

        if ($sale->type === 'cash') {
            $update['paid_total'] = $total;
        }

        $sale->update($update);

        return redirect()->route('sales.show', $sale);
    }

    public function show(Sale $sale): View
    {
        $sale->load(['customer', 'items.product', 'payments']);

        return view('sales.show', compact('sale'));
    }

    public function returnItem(Request $request, Sale $sale, StockService $stockService): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'numeric', 'min:0.001'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        $quantity = (float) $data['quantity'];

        $magasin = StockLocation::where('code', 'magasin')->firstOrFail();
        $balance = StockBalance::where('product_id', $product->id)
            ->where('location_id', $magasin->id)
            ->first();

        $unitCost = $balance?->avg_cost_local ?? $product->avg_cost_local;
        $unitPrice = $product->sale_price_local;

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'location_id' => $magasin->id,
            'quantity' => -$quantity,
            'unit_price' => $unitPrice,
            'unit_cost_local' => $unitCost,
            'discount_amount' => 0,
            'line_total' => -($unitPrice * $quantity),
        ]);

        $stockService->recordMovement([
            'product_id' => $product->id,
            'from_location_id' => null,
            'to_location_id' => $magasin->id,
            'quantity' => $quantity,
            'unit_cost_local' => $unitCost,
            'unit_sale_price_local' => $unitPrice,
            'type' => 'return_in',
            'reference_type' => Sale::class,
            'reference_id' => $sale->id,
            'occurred_at' => now(),
        ]);

        $sale->refresh();
        $subtotal = (float) $sale->items()->sum(DB::raw('unit_price * quantity'));
        $discountTotal = (float) $sale->items()->sum('discount_amount');
        $total = $subtotal - $discountTotal;

        $status = $sale->type === 'credit' && $sale->paid_total < $total ? 'open' : 'paid';

        $sale->update([
            'subtotal' => $subtotal,
            'discount_total' => $discountTotal,
            'total_amount' => $total,
            'status' => $status,
        ]);

        return redirect()->route('sales.show', $sale);
    }
}
