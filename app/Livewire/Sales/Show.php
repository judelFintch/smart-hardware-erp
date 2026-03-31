<?php

namespace App\Livewire\Sales;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleAdjustment;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\StockBalance;
use App\Models\StockLocation;
use App\Services\StockService;
use App\Support\LocationAccess;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Show extends Component
{
    public Sale $sale;

    public float $payment_amount = 0;
    public ?string $payment_paid_at = null;
    public string $payment_method = '';
    public string $payment_reference = '';
    public string $payment_notes = '';

    public string $adjustment_type = 'return';
    public ?int $return_product_id = null;
    public float $return_quantity = 0;
    public ?int $exchange_product_id = null;
    public float $exchange_quantity = 0;
    public string $return_condition = 'good';
    public string $return_notes = '';

    public function mount(Sale $sale): void
    {
        if (!LocationAccess::hasGlobalAccess()) {
            $allowedSale = LocationAccess::filterSales(Sale::query()->whereKey($sale->id))->exists();
            abort_unless($allowedSale, 403, 'Acces non autorise a cette vente.');
        }

        $this->sale = $sale->load(['customer', 'items.product', 'payments', 'adjustments.originalProduct', 'adjustments.replacementProduct', 'adjustments.location']);
        $this->return_product_id = data_get($this->getReturnableItems()->first(), 'product_id');
    }

    public function addPayment(): void
    {
        $data = $this->validate([
            'payment_amount' => ['required', 'numeric', 'min:0.01'],
            'payment_paid_at' => ['nullable', 'date'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'payment_notes' => ['nullable', 'string'],
        ]);

        SalePayment::create([
            'sale_id' => $this->sale->id,
            'amount' => (float) $data['payment_amount'],
            'paid_at' => $data['payment_paid_at'] ?? null,
            'method' => $data['payment_method'] ?? null,
            'reference' => $data['payment_reference'] ?? null,
            'notes' => $data['payment_notes'] ?? null,
        ]);

        $paidTotal = (float) $this->sale->payments()->sum('amount');
        $status = $paidTotal >= (float) $this->sale->total_amount ? 'paid' : 'open';

        $this->sale->update([
            'paid_total' => $paidTotal,
            'status' => $status,
        ]);

        $this->reset(['payment_amount', 'payment_paid_at', 'payment_method', 'payment_reference', 'payment_notes']);
        $this->sale->refresh()->load(['customer', 'items.product', 'payments']);
    }

    public function updatedAdjustmentType(string $value): void
    {
        if ($value === 'return') {
            $this->reset(['exchange_product_id', 'exchange_quantity']);
        }
    }

    public function updatedReturnProductId($value): void
    {
        if ((int) $value === 0) {
            $this->exchange_product_id = null;
        }
    }

    public function processAdjustment(StockService $stockService): void
    {
        $data = $this->validate([
            'adjustment_type' => ['required', Rule::in(['return', 'exchange'])],
            'return_product_id' => ['required', 'exists:products,id'],
            'return_quantity' => ['required', 'numeric', 'min:0.001'],
            'exchange_product_id' => [Rule::requiredIf($this->adjustment_type === 'exchange'), 'nullable', 'exists:products,id', 'different:return_product_id'],
            'exchange_quantity' => [Rule::requiredIf($this->adjustment_type === 'exchange'), 'nullable', 'numeric', 'min:0.001'],
            'return_condition' => ['required', Rule::in(['good', 'damaged', 'broken', 'defective', 'other'])],
            'return_notes' => ['nullable', 'string'],
        ]);

        $product = Product::findOrFail($data['return_product_id']);
        $quantity = (float) $data['return_quantity'];
        $availableToReverse = $this->getReturnableQuantity($product->id);

        if ($availableToReverse < $quantity) {
            $this->addError('return_quantity', "Quantité indisponible pour {$product->name}. Maximum: {$availableToReverse}.");
            return;
        }

        $saleItem = $this->sale->items()
            ->where('product_id', $product->id)
            ->where('quantity', '>', 0)
            ->latest('id')
            ->first();

        $magasin = $saleItem?->location_id
            ? StockLocation::findOrFail($saleItem->location_id)
            : StockLocation::findOrFail(LocationAccess::assignedLocationId() ?? StockLocation::where('code', 'magasin')->firstOrFail()->id);

        LocationAccess::ensureLocationAllowed($magasin->id);
        $balance = StockBalance::where('product_id', $product->id)
            ->where('location_id', $magasin->id)
            ->first();

        $unitCost = (float) ($balance?->avg_cost_local ?? $product->avg_cost_local);
        $unitPrice = (float) ($saleItem?->unit_price ?? $product->sale_price_local);
        $amount = $unitPrice * $quantity;

        $replacementProduct = null;
        $replacementQuantity = null;
        $replacementBalance = null;
        $replacementAvailable = 0;
        $replacementUnitCost = 0;
        $replacementUnitPrice = null;

        if ($data['adjustment_type'] === 'exchange') {
            $replacementProduct = Product::findOrFail($data['exchange_product_id']);
            $replacementQuantity = (float) $data['exchange_quantity'];
            $replacementBalance = StockBalance::query()
                ->where('product_id', $replacementProduct->id)
                ->where('location_id', $magasin->id)
                ->first();

            $replacementAvailable = (float) ($replacementBalance?->quantity ?? 0);
            if ($replacementAvailable < $replacementQuantity) {
                $this->addError('exchange_quantity', "Stock insuffisant pour {$replacementProduct->name}. Disponible: {$replacementAvailable}.");
                return;
            }

            $replacementUnitCost = (float) ($replacementBalance?->avg_cost_local ?? $replacementProduct->avg_cost_local);
            $replacementUnitPrice = $replacementQuantity > 0
                ? round($amount / $replacementQuantity, 2)
                : 0;
        }

        DB::transaction(function () use ($data, $stockService, $product, $quantity, $magasin, $unitCost, $unitPrice, $amount, $replacementProduct, $replacementQuantity, $replacementUnitCost, $replacementUnitPrice) {

            SaleItem::create([
                'sale_id' => $this->sale->id,
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
                'reference_id' => $this->sale->id,
                'occurred_at' => now(),
                'note' => $this->buildStockNote($data['adjustment_type'], $data['return_condition'], $data['return_notes'] ?? null),
            ]);

            if ($data['adjustment_type'] === 'exchange') {
                SaleItem::create([
                    'sale_id' => $this->sale->id,
                    'product_id' => $replacementProduct->id,
                    'location_id' => $magasin->id,
                    'quantity' => $replacementQuantity,
                    'unit_price' => $replacementUnitPrice,
                    'unit_cost_local' => $replacementUnitCost,
                    'discount_amount' => 0,
                    'line_total' => $amount,
                ]);

                $stockService->recordMovement([
                    'product_id' => $replacementProduct->id,
                    'from_location_id' => $magasin->id,
                    'to_location_id' => null,
                    'quantity' => $replacementQuantity,
                    'unit_cost_local' => $replacementUnitCost,
                    'unit_sale_price_local' => $replacementUnitPrice,
                    'type' => 'sale_out',
                    'reference_type' => Sale::class,
                    'reference_id' => $this->sale->id,
                    'occurred_at' => now(),
                    'note' => $this->buildStockNote('exchange', $data['return_condition'], $data['return_notes'] ?? null, $replacementProduct->name),
                ]);
            }

            SaleAdjustment::create([
                'sale_id' => $this->sale->id,
                'location_id' => $magasin->id,
                'original_product_id' => $product->id,
                'original_quantity' => $quantity,
                'original_unit_price' => $unitPrice,
                'replacement_product_id' => $replacementProduct?->id,
                'replacement_quantity' => $replacementQuantity,
                'replacement_unit_price' => $replacementUnitPrice,
                'type' => $data['adjustment_type'],
                'item_condition' => $data['return_condition'],
                'amount_local' => $amount,
                'notes' => $data['return_notes'] ?? null,
                'processed_at' => now(),
                'created_by' => auth()->id(),
            ]);

            $this->sale->refresh();
            $subtotal = (float) $this->sale->items()->sum('line_total');
            $discountTotal = min((float) $this->sale->discount_total, max(0, $subtotal));
            $total = max(0, $subtotal - $discountTotal);

            $status = $this->sale->type === 'credit' && $this->sale->paid_total < $total ? 'open' : 'paid';

            $this->sale->update([
                'subtotal' => $subtotal,
                'discount_total' => $discountTotal,
                'total_amount' => $total,
                'status' => $status,
            ]);
        });

        $this->reset(['return_quantity', 'exchange_product_id', 'exchange_quantity', 'return_condition', 'return_notes']);
        $this->adjustment_type = 'return';
        $this->return_condition = 'good';
        $this->sale->refresh()->load(['customer', 'items.product', 'payments', 'adjustments.originalProduct', 'adjustments.replacementProduct', 'adjustments.location']);
        $this->return_product_id = data_get($this->getReturnableItems()->first(), 'product_id');
    }

    public function render()
    {
        $returnableItems = $this->getReturnableItems();
        $selectedLocationId = $this->getReturnLocationId($this->return_product_id);
        $exchangeableProducts = collect();
        $exchangeStocks = [];

        if ($selectedLocationId) {
            $exchangeableProducts = Product::query()
                ->select('products.*')
                ->join('stock_balances', 'stock_balances.product_id', '=', 'products.id')
                ->where('stock_balances.location_id', $selectedLocationId)
                ->where('stock_balances.quantity', '>', 0)
                ->orderBy('products.name')
                ->get()
                ->unique('id')
                ->values();

            $exchangeStocks = StockBalance::query()
                ->where('location_id', $selectedLocationId)
                ->where('quantity', '>', 0)
                ->pluck('quantity', 'product_id')
                ->map(fn ($quantity) => (float) $quantity)
                ->all();
        }

        return view('livewire.sales.show', compact('returnableItems', 'exchangeableProducts', 'exchangeStocks'))
            ->layout('layouts.app');
    }

    private function getReturnableItems()
    {
        return $this->sale->items
            ->groupBy('product_id')
            ->map(function ($items, $productId) {
                $netQuantity = (float) $items->sum('quantity');
                $sourceItem = $items->firstWhere('quantity', '>', 0) ?? $items->first();

                if ($netQuantity <= 0 || !$sourceItem?->product) {
                    return null;
                }

                return [
                    'product_id' => (int) $productId,
                    'name' => $sourceItem->product->name,
                    'quantity' => $netQuantity,
                    'location_id' => (int) $sourceItem->location_id,
                ];
            })
            ->filter()
            ->sortBy('name')
            ->values();
    }

    private function getReturnableQuantity(int $productId): float
    {
        return max(0, (float) $this->sale->items()->where('product_id', $productId)->sum('quantity'));
    }

    private function getReturnLocationId(?int $productId): ?int
    {
        if (!$productId) {
            return null;
        }

        return $this->sale->items
            ->where('product_id', $productId)
            ->where('quantity', '>', 0)
            ->sortByDesc('id')
            ->first()?->location_id;
    }

    private function buildStockNote(string $type, string $condition, ?string $notes = null, ?string $replacementProduct = null): string
    {
        $conditionLabel = $this->conditionLabel($condition);
        $prefix = $type === 'exchange'
            ? "Échange ({$conditionLabel})"
            : "Retour ({$conditionLabel})";

        if ($replacementProduct) {
            $prefix .= " contre {$replacementProduct}";
        }

        return trim($prefix . ($notes ? " - {$notes}" : ''));
    }

    public function conditionLabel(string $condition): string
    {
        return match ($condition) {
            'good' => 'Bon état',
            'damaged' => 'Endommagé',
            'broken' => 'Foutu',
            'defective' => 'Défectueux',
            default => 'Autre',
        };
    }
}
