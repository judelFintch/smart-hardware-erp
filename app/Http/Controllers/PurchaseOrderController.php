<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseTransfer;
use App\Models\StockLocation;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    public function index(): View
    {
        $purchases = PurchaseOrder::with('supplier')->orderByDesc('id')->get();

        return view('purchases.index', compact('purchases'));
    }

    public function export(): StreamedResponse
    {
        $purchases = PurchaseOrder::with('supplier')->orderByDesc('id')->get();

        return response()->streamDownload(function () use ($purchases) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Fournisseur', 'Type', 'Statut', 'Total', 'Commande', 'Réception']);

            foreach ($purchases as $purchase) {
                fputcsv($handle, [
                    $purchase->id,
                    $purchase->supplier->name,
                    $purchase->type,
                    $purchase->status,
                    $purchase->total_cost_local,
                    $purchase->ordered_at,
                    $purchase->received_at,
                ]);
            }

            fclose($handle);
        }, 'purchases.csv', ['Content-Type' => 'text/csv']);
    }

    public function create(): View
    {
        $suppliers = \App\Models\Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request, StockService $stockService): RedirectResponse
    {
        $items = array_values(array_filter($request->input('items', []), function ($item) {
            return !empty($item['product_id']) && !empty($item['quantity']);
        }));

        $payload = $request->all();
        $payload['items'] = $items;

        $data = \\Illuminate\\Support\\Facades\\Validator::make($payload, [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'type' => ['required', 'in:local,foreign'],
            'status' => ['required', 'in:en_cours,en_transit,receptionnee'],
            'reference' => ['nullable', 'string', 'max:255'],
            'ordered_at' => ['nullable', 'date'],
            'in_transit_at' => ['nullable', 'date'],
            'received_at' => ['nullable', 'date'],
            'currency' => ['nullable', 'string', 'max:10'],
            'exchange_rate' => ['nullable', 'numeric', 'min:0'],
            'accessory_fees_local' => ['nullable', 'numeric', 'min:0'],
            'transport_fees_local' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ])->validate();

        $exchangeRate = (float) ($data['exchange_rate'] ?? 1);
        $currency = $data['currency'] ?? 'CDF';

        $purchase = PurchaseOrder::create([
            'supplier_id' => $data['supplier_id'],
            'type' => $data['type'],
            'status' => $data['status'],
            'reference' => $data['reference'] ?? null,
            'ordered_at' => $data['ordered_at'] ?? null,
            'in_transit_at' => $data['in_transit_at'] ?? null,
            'received_at' => $data['received_at'] ?? null,
            'currency' => $currency,
            'exchange_rate' => $exchangeRate,
            'accessory_fees_local' => (float) ($data['accessory_fees_local'] ?? 0),
            'transport_fees_local' => (float) ($data['transport_fees_local'] ?? 0),
            'notes' => $data['notes'] ?? null,
        ]);

        $subtotalForeign = 0;
        $subtotalLocal = 0;
        $totalQty = 0;

        foreach ($data['items'] as $item) {
            $qty = (float) $item['quantity'];
            $unitPrice = (float) $item['unit_price'];
            $lineForeign = $data['type'] === 'foreign' ? $qty * $unitPrice : 0;
            $lineLocal = $data['type'] === 'foreign'
                ? $lineForeign * $exchangeRate
                : $qty * $unitPrice;

            $subtotalForeign += $lineForeign;
            $subtotalLocal += $lineLocal;
            $totalQty += $qty;

            PurchaseOrderItem::create([
                'purchase_order_id' => $purchase->id,
                'product_id' => $item['product_id'],
                'quantity' => $qty,
                'unit_price_foreign' => $data['type'] === 'foreign' ? $unitPrice : 0,
                'unit_price_local' => $data['type'] === 'foreign' ? $unitPrice * $exchangeRate : $unitPrice,
                'line_total_foreign' => $lineForeign,
                'line_total_local' => $lineLocal,
                'unit_cost_local' => 0,
            ]);
        }

        $accessoryFees = (float) ($data['accessory_fees_local'] ?? 0);
        $transportFees = (float) ($data['transport_fees_local'] ?? 0);
        $totalCostLocal = $subtotalLocal + $accessoryFees + $transportFees;

        $purchase->update([
            'subtotal_foreign' => $subtotalForeign,
            'subtotal_local' => $subtotalLocal,
            'total_cost_local' => $totalCostLocal,
        ]);

        $this->allocateCostToItems($purchase, $accessoryFees + $transportFees, $totalQty);

        if ($purchase->status === 'receptionnee') {
            $this->receivePurchase($purchase, $stockService);
        }

        return redirect()->route('purchases.show', $purchase);
    }

    public function show(PurchaseOrder $purchaseOrder): View
    {
        $purchaseOrder->load(['supplier', 'items.product', 'transfers']);

        return view('purchases.show', compact('purchaseOrder'));
    }

    public function storeTransfer(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $data = $request->validate([
            'amount_foreign' => ['nullable', 'numeric', 'min:0'],
            'amount_local' => ['nullable', 'numeric', 'min:0'],
            'paid_at' => ['nullable', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        PurchaseTransfer::create([
            'purchase_order_id' => $purchaseOrder->id,
            'amount_foreign' => (float) ($data['amount_foreign'] ?? 0),
            'amount_local' => (float) ($data['amount_local'] ?? 0),
            'paid_at' => $data['paid_at'] ?? null,
            'reference' => $data['reference'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('purchases.show', $purchaseOrder);
    }

    public function receive(PurchaseOrder $purchaseOrder, StockService $stockService): RedirectResponse
    {
        $purchaseOrder->load('items');

        if ($purchaseOrder->status !== 'receptionnee') {
            $purchaseOrder->update([
                'status' => 'receptionnee',
                'received_at' => $purchaseOrder->received_at ?? now()->toDateString(),
            ]);
        }

        $this->receivePurchase($purchaseOrder, $stockService);

        return redirect()->route('purchases.show', $purchaseOrder);
    }

    private function receivePurchase(PurchaseOrder $purchaseOrder, StockService $stockService): void
    {
        $depot = StockLocation::where('code', 'depot')->firstOrFail();

        foreach ($purchaseOrder->items as $item) {
            $product = $item->product;
            $unitCost = (float) $item->unit_cost_local;
            $unitSale = (float) $product->sale_price_local;

            $stockService->recordMovement([
                'product_id' => $item->product_id,
                'from_location_id' => null,
                'to_location_id' => $depot->id,
                'quantity' => $item->quantity,
                'unit_cost_local' => $unitCost,
                'unit_sale_price_local' => $unitSale,
                'type' => 'purchase_in',
                'reference_type' => PurchaseOrder::class,
                'reference_id' => $purchaseOrder->id,
                'occurred_at' => $purchaseOrder->received_at ?? now(),
            ]);
        }
    }

    private function allocateCostToItems(PurchaseOrder $purchase, float $extraFees, float $totalQty): void
    {
        if ($extraFees <= 0 || $totalQty <= 0) {
            foreach ($purchase->items as $item) {
                $item->update([
                    'unit_cost_local' => $item->unit_price_local,
                ]);
            }
            return;
        }

        $extraPerUnit = $extraFees / $totalQty;

        foreach ($purchase->items as $item) {
            $item->update([
                'unit_cost_local' => $item->unit_price_local + $extraPerUnit,
            ]);
        }
    }
}
