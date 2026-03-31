<?php

namespace App\Livewire\Sales;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockBalance;
use App\Models\StockLocation;
use App\Services\StockService;
use App\Support\LocationAccess;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{
    public string $type = 'cash';
    public string $product_search = '';
    public ?int $customer_id = null;
    public ?int $location_id = null;
    public ?string $sold_at = null;
    public float $global_discount_amount = 0;
    public array $items = [];

    public function mount(): void
    {
        $this->location_id = LocationAccess::assignedLocationId()
            ?? StockLocation::where('code', 'magasin')->first()?->id;

        $this->items = [
            ['product_id' => null, 'quantity' => null],
            ['product_id' => null, 'quantity' => null],
            ['product_id' => null, 'quantity' => null],
        ];
    }

    public function addItem(): void
    {
        $this->items[] = ['product_id' => null, 'quantity' => null];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function addProduct(int $productId): void
    {
        if (!$this->location_id) {
            $this->addError('product_search', 'Choisis d’abord le magasin de vente.');
            return;
        }

        $product = Product::find($productId);

        if (!$product) {
            $this->addError('product_search', 'Article introuvable.');
            return;
        }

        $availableStock = $this->getItemAvailableStock($product->id);

        if ($availableStock <= 0) {
            $this->addError('product_search', "Aucun stock disponible pour {$product->name}.");
            return;
        }

        foreach ($this->items as $index => $item) {
            if ((int) ($item['product_id'] ?? 0) !== $product->id) {
                continue;
            }

            $currentQuantity = (float) ($item['quantity'] ?? 0);
            $nextQuantity = $currentQuantity + 1;

            if ($nextQuantity > $availableStock) {
                $this->addError('product_search', "Stock insuffisant pour ajouter une unité de plus sur {$product->name}.");
                return;
            }

            $this->items[$index]['quantity'] = number_format($nextQuantity, 3, '.', '');
            $this->product_search = '';
            $this->resetErrorBag('product_search');

            return;
        }

        $targetIndex = collect($this->items)
            ->search(fn (array $item) => empty($item['product_id']));

        if ($targetIndex === false) {
            $this->addItem();
            $targetIndex = array_key_last($this->items);
        }

        $this->items[$targetIndex] = [
            'product_id' => $product->id,
            'quantity' => '1.000',
        ];

        $this->product_search = '';
        $this->resetErrorBag('product_search');
    }

    public function getItemUnitPrice(?int $productId): float
    {
        if (!$productId) {
            return 0;
        }

        $product = Product::find($productId);
        if (!$product) {
            return 0;
        }

        $balance = $this->location_id
            ? StockBalance::where('product_id', $product->id)->where('location_id', $this->location_id)->first()
            : null;

        return $this->resolveSalePrice($product, $balance);
    }

    public function getItemAvailableStock(?int $productId): float
    {
        if (!$productId || !$this->location_id) {
            return 0;
        }

        return (float) (StockBalance::where('product_id', $productId)
            ->where('location_id', $this->location_id)
            ->value('quantity') ?? 0);
    }

    public function getItemLineTotal(array $item): float
    {
        $quantity = (float) ($item['quantity'] ?? 0);
        $unitPrice = $this->getItemUnitPrice(isset($item['product_id']) ? (int) $item['product_id'] : null);

        return max(0, $unitPrice * $quantity);
    }

    public function getSubtotalPreview(): float
    {
        return collect($this->items)->sum(fn (array $item) => $this->getItemLineTotal($item));
    }

    public function getTotalPreview(): float
    {
        return max(0, $this->getSubtotalPreview() - (float) $this->global_discount_amount);
    }

    public function save(StockService $stockService): void
    {
        $filteredItems = array_values(array_filter($this->items, function ($item) {
            return !empty($item['product_id']) && !empty($item['quantity']);
        }));

        $data = $this->validate([
            'type' => ['required', 'in:cash,credit'],
            'customer_id' => ['nullable', 'required_if:type,credit', 'exists:customers,id'],
            'location_id' => ['required', 'exists:stock_locations,id'],
            'sold_at' => ['nullable', 'date'],
            'global_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.quantity' => ['nullable', 'numeric', 'min:0.001'],
        ]);

        if (count($filteredItems) === 0) {
            $this->addError('items', 'Ajoute au moins un article.');
            return;
        }

        LocationAccess::ensureLocationAllowed($data['location_id']);
        $location = StockLocation::findOrFail($data['location_id']);
        foreach ($filteredItems as $item) {
            $product = Product::findOrFail($item['product_id']);
            $quantity = (float) $item['quantity'];

            $balance = StockBalance::where('product_id', $product->id)
                ->where('location_id', $location->id)
                ->first();

            $available = (float) ($balance?->quantity ?? 0);
            if ($available < $quantity) {
                $this->addError('items', "Stock insuffisant pour {$product->name} dans {$location->name} (disponible: {$available}).");
                return;
            }
        }

        DB::transaction(function () use ($data, $filteredItems, $stockService, $location) {
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

            $subtotal = 0;
            $discountTotal = 0;

            foreach ($filteredItems as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = (float) $item['quantity'];

                $balance = StockBalance::where('product_id', $product->id)
                    ->where('location_id', $location->id)
                    ->first();

                $available = (float) ($balance?->quantity ?? 0);
                if ($available < $quantity) {
                    $this->addError('items', "Stock insuffisant pour {$product->name} dans {$location->name} (disponible: {$available}).");
                    continue;
                }

                $unitCost = $balance?->avg_cost_local ?? $product->avg_cost_local;
                $unitPrice = $this->resolveSalePrice($product, $balance);
                if ($unitPrice > 0 && (float) $product->sale_price_local <= 0) {
                    $product->update(['sale_price_local' => $unitPrice]);
                }

                $lineTotal = $unitPrice * $quantity;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'location_id' => $location->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'unit_cost_local' => $unitCost,
                    'discount_amount' => 0,
                    'line_total' => $lineTotal,
                ]);

                $stockService->recordMovement([
                    'product_id' => $product->id,
                    'from_location_id' => $location->id,
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
            }

            $discountTotal = min((float) ($data['global_discount_amount'] ?? 0), $subtotal);
            $total = max(0, $subtotal - $discountTotal);

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

    private function resolveSalePrice(Product $product, ?StockBalance $balance): float
    {
        $price = (float) $product->sale_price_local;
        if ($price > 0) {
            return $price;
        }

        $baseCost = (float) ($balance?->avg_cost_local ?? $product->avg_cost_local);
        if ($baseCost <= 0) {
            return 0;
        }

        $margin = (float) ($product->sale_margin_percent ?? 0);
        if ($margin <= 0) {
            return $baseCost;
        }

        return $baseCost * (1 + ($margin / 100));
    }

    public function render()
    {
        $customers = Customer::orderBy('name')->get();
        $locations = LocationAccess::restrictLocations(StockLocation::query()->orderBy('name'))->get();
        $products = Product::orderBy('name')->get();
        $quickProducts = collect();

        if (trim($this->product_search) !== '' && $this->location_id) {
            $search = '%' . trim($this->product_search) . '%';

            $quickProducts = Product::query()
                ->select('products.*')
                ->join('stock_balances', 'stock_balances.product_id', '=', 'products.id')
                ->where('stock_balances.location_id', $this->location_id)
                ->where('stock_balances.quantity', '>', 0)
                ->where(function ($query) use ($search) {
                    $query->where('products.name', 'like', $search)
                        ->orWhere('products.sku', 'like', $search)
                        ->orWhere('products.barcode', 'like', $search);
                })
                ->orderBy('products.name')
                ->limit(8)
                ->get()
                ->unique('id')
                ->values();
        }

        $canSelectAnyLocation = LocationAccess::hasGlobalAccess();

        return view('livewire.sales.create', compact('customers', 'locations', 'products', 'quickProducts', 'canSelectAnyLocation'))
            ->layout('layouts.app');
    }
}
