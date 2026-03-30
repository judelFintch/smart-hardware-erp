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

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function save(StockService $stockService): void
    {
        $data = $this->validate([
            'from_location_id' => ['required', 'exists:stock_locations,id'],
            'to_location_id' => ['required', 'exists:stock_locations,id', 'different:from_location_id'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
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
        $products = Product::orderBy('name')->get();
        $fromLocations = LocationAccess::restrictLocations(StockLocation::query()->orderBy('name'))->get();
        $toLocations = StockLocation::query()
            ->when(LocationAccess::assignedLocationId(), fn ($query, $locationId) => $query->whereKeyNot($locationId))
            ->orderBy('name')
            ->get();
        $canSelectAnyLocation = LocationAccess::hasGlobalAccess();

        return view('livewire.stock-transfers.create', compact('products', 'fromLocations', 'toLocations', 'canSelectAnyLocation'))
            ->layout('layouts.app');
    }
}
