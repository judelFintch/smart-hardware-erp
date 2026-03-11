<?php

namespace App\Livewire\Purchases;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StockLocation;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{
    public ?int $supplier_id = null;
    public string $type = 'local';
    public string $status = 'en_cours';
    public string $reference = '';
    public ?string $ordered_at = null;
    public ?string $in_transit_at = null;
    public ?string $received_at = null;
    public string $currency = 'CDF';
    public float $exchange_rate = 1;
    public float $accessory_fees_local = 0;
    public float $transport_fees_local = 0;
    public string $notes = '';
    public array $items = [];

    public function mount(): void
    {
        $this->items = [
            ['product_id' => null, 'quantity' => null, 'unit_price' => null],
            ['product_id' => null, 'quantity' => null, 'unit_price' => null],
            ['product_id' => null, 'quantity' => null, 'unit_price' => null],
        ];
    }

    public function addItem(): void
    {
        $this->items[] = ['product_id' => null, 'quantity' => null, 'unit_price' => null];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function save(StockService $stockService): void
    {
        $filteredItems = array_values(array_filter($this->items, function ($item) {
            return !empty($item['product_id']) && !empty($item['quantity']);
        }));

        $data = $this->validate([
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
        ]);

        if (count($filteredItems) === 0) {
            $this->addError('items', 'Ajoute au moins un article.');
            return;
        }
        foreach ($filteredItems as $item) {
            if (!isset($item['unit_price']) || $item['unit_price'] === '' || !is_numeric($item['unit_price'])) {
                $this->addError('items', 'Chaque article doit avoir un prix unitaire.');
                return;
            }
        }

        DB::transaction(function () use ($data, $filteredItems, $stockService) {
            $purchase = PurchaseOrder::create([
                'supplier_id' => $data['supplier_id'],
                'type' => $data['type'],
                'status' => $data['status'],
                'reference' => $data['reference'] ?? null,
                'ordered_at' => $data['ordered_at'] ?? null,
                'in_transit_at' => $data['in_transit_at'] ?? null,
                'received_at' => $data['received_at'] ?? null,
                'currency' => $data['currency'] ?? 'CDF',
                'exchange_rate' => (float) ($data['exchange_rate'] ?? 1),
                'accessory_fees_local' => (float) ($data['accessory_fees_local'] ?? 0),
                'transport_fees_local' => (float) ($data['transport_fees_local'] ?? 0),
                'notes' => $data['notes'] ?? null,
            ]);

            $subtotalForeign = 0;
            $subtotalLocal = 0;
            $totalQty = 0;

            foreach ($filteredItems as $item) {
                $qty = (float) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];
                $lineForeign = $data['type'] === 'foreign' ? $qty * $unitPrice : 0;
                $lineLocal = $data['type'] === 'foreign'
                    ? $lineForeign * (float) $data['exchange_rate']
                    : $qty * $unitPrice;

                $subtotalForeign += $lineForeign;
                $subtotalLocal += $lineLocal;
                $totalQty += $qty;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $qty,
                    'unit_price_foreign' => $data['type'] === 'foreign' ? $unitPrice : 0,
                    'unit_price_local' => $data['type'] === 'foreign' ? $unitPrice * (float) $data['exchange_rate'] : $unitPrice,
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
        });

        $this->redirectRoute('purchases.index');
    }

    private function receivePurchase(PurchaseOrder $purchase, StockService $stockService): void
    {
        $alreadyReceived = StockMovement::where('reference_type', PurchaseOrder::class)
            ->where('reference_id', $purchase->id)
            ->where('type', 'purchase_in')
            ->exists();

        if ($alreadyReceived) {
            return;
        }

        $depot = StockLocation::where('code', 'depot')->firstOrFail();
        $purchase->load('items.product');

        foreach ($purchase->items as $item) {
            $unitCost = (float) $item->unit_cost_local;
            $unitSale = (float) $item->product->sale_price_local;

            $stockService->recordMovement([
                'product_id' => $item->product_id,
                'from_location_id' => null,
                'to_location_id' => $depot->id,
                'quantity' => $item->quantity,
                'unit_cost_local' => $unitCost,
                'unit_sale_price_local' => $unitSale,
                'type' => 'purchase_in',
                'reference_type' => PurchaseOrder::class,
                'reference_id' => $purchase->id,
                'occurred_at' => $purchase->received_at ?? now(),
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

    public function render()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('livewire.purchases.create', compact('suppliers', 'products'))
            ->layout('layouts.app');
    }
}
