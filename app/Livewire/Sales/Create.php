<?php

namespace App\Livewire\Sales;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockBalance;
use App\Models\StockLocation;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{
    public string $type = 'cash';
    public ?int $customer_id = null;
    public ?string $sold_at = null;
    public array $items = [];

    public function mount(): void
    {
        $this->items = [
            ['product_id' => null, 'quantity' => null, 'discount_amount' => 0],
            ['product_id' => null, 'quantity' => null, 'discount_amount' => 0],
            ['product_id' => null, 'quantity' => null, 'discount_amount' => 0],
        ];
    }

    public function addItem(): void
    {
        $this->items[] = ['product_id' => null, 'quantity' => null, 'discount_amount' => 0];
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
            'type' => ['required', 'in:cash,credit'],
            'customer_id' => ['nullable', 'required_if:type,credit', 'exists:customers,id'],
            'sold_at' => ['nullable', 'date'],
        ]);

        if (count($filteredItems) === 0) {
            $this->addError('items', 'Ajoute au moins un article.');
            return;
        }

        $magasin = StockLocation::where('code', 'magasin')->firstOrFail();
        foreach ($filteredItems as $item) {
            $product = Product::findOrFail($item['product_id']);
            $quantity = (float) $item['quantity'];

            $balance = StockBalance::where('product_id', $product->id)
                ->where('location_id', $magasin->id)
                ->first();

            $available = (float) ($balance?->quantity ?? 0);
            if ($available < $quantity) {
                $this->addError('items', "Stock insuffisant pour {$product->name} (disponible: {$available}).");
                return;
            }
        }

        DB::transaction(function () use ($data, $filteredItems, $stockService) {
            $sale = Sale::create([
                'customer_id' => $data['customer_id'] ?? null,
                'type' => $data['type'],
                'status' => $data['type'] === 'cash' ? 'paid' : 'open',
                'subtotal' => 0,
                'discount_total' => 0,
                'total_amount' => 0,
                'paid_total' => 0,
                'sold_at' => $data['sold_at'] ?? now(),
            ]);

            $magasin = StockLocation::where('code', 'magasin')->firstOrFail();

            $subtotal = 0;
            $discountTotal = 0;

            foreach ($filteredItems as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = (float) $item['quantity'];
                $unitPrice = (float) $product->sale_price_local;
                $discount = (float) ($item['discount_amount'] ?? 0);

                $maxDiscount = $unitPrice * $quantity;
                if ($discount > $maxDiscount) {
                    $discount = $maxDiscount;
                }

                $balance = StockBalance::where('product_id', $product->id)
                    ->where('location_id', $magasin->id)
                    ->first();

                $available = (float) ($balance?->quantity ?? 0);
                if ($available < $quantity) {
                    $this->addError('items', "Stock insuffisant pour {$product->name} (disponible: {$available}).");
                    continue;
                }

                $unitCost = $balance?->avg_cost_local ?? $product->avg_cost_local;
                $lineTotal = ($unitPrice * $quantity) - $discount;

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
        });

        $this->redirectRoute('sales.index');
    }

    public function render()
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('livewire.sales.create', compact('customers', 'products'))
            ->layout('layouts.app');
    }
}
