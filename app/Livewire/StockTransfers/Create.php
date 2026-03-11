<?php

namespace App\Livewire\StockTransfers;

use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockLocation;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{
    public array $items = [];

    public function mount(): void
    {
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
        $filteredItems = array_values(array_filter($this->items, function ($item) {
            return !empty($item['product_id']) && !empty($item['quantity']);
        }));

        if (count($filteredItems) === 0) {
            $this->addError('items', 'Ajoute au moins un article.');
            return;
        }

        $depot = StockLocation::where('code', 'depot')->firstOrFail();
        $magasin = StockLocation::where('code', 'magasin')->firstOrFail();

        foreach ($filteredItems as $item) {
            $product = Product::findOrFail($item['product_id']);
            $quantity = (float) $item['quantity'];

            $balance = StockBalance::where('product_id', $product->id)
                ->where('location_id', $depot->id)
                ->first();

            $available = (float) ($balance?->quantity ?? 0);
            if ($available < $quantity) {
                $this->addError('items', "Stock insuffisant pour {$product->name} (disponible: {$available}).");
                return;
            }
        }

        DB::transaction(function () use ($filteredItems, $stockService, $depot, $magasin) {
            foreach ($filteredItems as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = (float) $item['quantity'];

                $balance = StockBalance::where('product_id', $product->id)
                    ->where('location_id', $depot->id)
                    ->first();

                $unitCost = $balance?->avg_cost_local ?? $product->avg_cost_local;

                $stockService->recordMovement([
                    'product_id' => $product->id,
                    'from_location_id' => $depot->id,
                    'to_location_id' => $magasin->id,
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

        $this->redirectRoute('dashboard');
    }

    public function render()
    {
        $products = Product::orderBy('name')->get();

        return view('livewire.stock-transfers.create', compact('products'))
            ->layout('layouts.app');
    }
}
