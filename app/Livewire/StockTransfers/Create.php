<?php

namespace App\Livewire\StockTransfers;

use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockLocation;
use App\Services\StockService;
use App\Support\LocationAccess;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{
    public ?int $from_location_id = null;
    public ?int $to_location_id = null;
    public array $items = [];

    public function mount(): void
    {
        $this->from_location_id = LocationAccess::assignedLocationId();
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

    public function updatedFromLocationId($value): void
    {
        $locationId = $value ? (int) $value : null;

        if ($locationId && $this->to_location_id === $locationId) {
            $this->to_location_id = null;
        }

        $availableProductIds = $locationId
            ? StockBalance::query()
                ->where('location_id', $locationId)
                ->where('quantity', '>', 0)
                ->pluck('product_id')
                ->map(fn ($productId) => (int) $productId)
                ->all()
            : [];

        foreach ($this->items as $index => $item) {
            $selectedProductId = (int) ($item['product_id'] ?? 0);

            if ($selectedProductId !== 0 && !in_array($selectedProductId, $availableProductIds, true)) {
                $this->items[$index]['product_id'] = null;
                $this->items[$index]['quantity'] = null;
            }
        }
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function fillMaxQuantity(int $index): void
    {
        $productId = (int) ($this->items[$index]['product_id'] ?? 0);

        if (!$this->from_location_id || $productId === 0) {
            return;
        }

        $availableQuantity = (float) (StockBalance::query()
            ->where('location_id', $this->from_location_id)
            ->where('product_id', $productId)
            ->value('quantity') ?? 0);

        $this->items[$index]['quantity'] = $availableQuantity > 0
            ? number_format($availableQuantity, 3, '.', '')
            : null;
    }

    public function save(StockService $stockService): void
    {
        $data = $this->validate([
            'from_location_id' => ['required', 'exists:stock_locations,id'],
            'to_location_id' => ['required', 'exists:stock_locations,id', 'different:from_location_id'],
            'items.*.product_id' => ['nullable', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['nullable', 'numeric', 'min:0.001'],
        ]);

        $filteredItems = array_values(array_filter($this->items, function ($item) {
            return !empty($item['product_id']) && !empty($item['quantity']);
        }));

        if (count($filteredItems) === 0) {
            $this->addError('items', 'Ajoute au moins un article.');
            return;
        }

        LocationAccess::ensureLocationAllowed((int) $data['from_location_id']);
        $from = StockLocation::findOrFail($data['from_location_id']);
        $to = StockLocation::findOrFail($data['to_location_id']);

        foreach ($filteredItems as $item) {
            $product = Product::findOrFail($item['product_id']);
            $quantity = (float) $item['quantity'];

            $balance = StockBalance::where('product_id', $product->id)
                ->where('location_id', $from->id)
                ->first();

            $available = (float) ($balance?->quantity ?? 0);
            if ($available < $quantity) {
                $this->addError('items', "Stock insuffisant pour {$product->name} (disponible: {$available}).");
                return;
            }
        }

        DB::transaction(function () use ($filteredItems, $stockService, $from, $to) {
            foreach ($filteredItems as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = (float) $item['quantity'];

                $balance = StockBalance::where('product_id', $product->id)
                    ->where('location_id', $from->id)
                    ->first();

                $unitCost = $balance?->avg_cost_local ?? $product->avg_cost_local;

                $stockService->recordMovement([
                    'product_id' => $product->id,
                    'from_location_id' => $from->id,
                    'to_location_id' => $to->id,
                    'quantity' => $quantity,
                    'unit_cost_local' => (float) $unitCost,
                    'unit_sale_price_local' => (float) $product->sale_price_local,
                    'type' => 'transfer_in',
                    'reference_type' => 'stock_transfer',
                    'reference_id' => null,
                    'occurred_at' => now(),
                ]);
            }
        });

        $this->redirectRoute('stock-movements.index');
    }

    public function render()
    {
        $availableProducts = collect();
        $availableQuantities = [];

        if ($this->from_location_id) {
            $availableProducts = Product::query()
                ->select('products.*')
                ->join('stock_balances', 'stock_balances.product_id', '=', 'products.id')
                ->where('stock_balances.location_id', $this->from_location_id)
                ->where('stock_balances.quantity', '>', 0)
                ->orderBy('products.name')
                ->get()
                ->unique('id')
                ->values();

            $availableQuantities = StockBalance::query()
                ->where('location_id', $this->from_location_id)
                ->where('quantity', '>', 0)
                ->pluck('quantity', 'product_id')
                ->map(fn ($quantity) => (float) $quantity)
                ->all();
        }

        $fromLocations = LocationAccess::restrictLocations(StockLocation::query()->orderBy('name'))->get();
        $toLocations = StockLocation::query()
            ->when($this->from_location_id, fn ($query, $locationId) => $query->whereKeyNot($locationId))
            ->orderBy('name')
            ->get();
        $canSelectAnyLocation = LocationAccess::hasGlobalAccess();
        $fromLocation = $this->from_location_id ? $fromLocations->firstWhere('id', $this->from_location_id) : null;
        $toLocation = $this->to_location_id ? $toLocations->firstWhere('id', $this->to_location_id) : null;

        return view('livewire.stock-transfers.create', compact(
            'availableProducts',
            'availableQuantities',
            'fromLocations',
            'toLocations',
            'canSelectAnyLocation',
            'fromLocation',
            'toLocation'
        ))
            ->layout('layouts.app');
    }
}
