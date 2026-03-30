<?php

namespace App\Livewire\InventoryCounts;

use App\Models\InventoryCount;
use App\Models\InventoryCountItem;
use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockLocation;
use App\Services\StockService;
use App\Support\LocationAccess;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{
    public ?int $location_id = null;
    public ?string $counted_at = null;
    public string $notes = '';
    public array $items = [];

    public function mount(): void
    {
        $this->location_id = LocationAccess::assignedLocationId();
        $this->items = [
            ['product_id' => null, 'counted_quantity' => null],
            ['product_id' => null, 'counted_quantity' => null],
            ['product_id' => null, 'counted_quantity' => null],
        ];
    }

    public function addItem(): void
    {
        $this->items[] = ['product_id' => null, 'counted_quantity' => null];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function save(StockService $stockService): void
    {
        $filteredItems = array_values(array_filter($this->items, function ($item) {
            return !empty($item['product_id']) && $item['counted_quantity'] !== null && $item['counted_quantity'] !== '';
        }));

        $data = $this->validate([
            'location_id' => ['required', 'exists:stock_locations,id'],
            'counted_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.counted_quantity' => ['nullable', 'numeric', 'min:0'],
        ]);

        if (count($filteredItems) === 0) {
            $this->addError('items', 'Ajoute au moins un article.');
            return;
        }

        LocationAccess::ensureLocationAllowed((int) $data['location_id']);
        DB::transaction(function () use ($data, $filteredItems, $stockService) {
            $inventory = InventoryCount::create([
                'location_id' => $data['location_id'],
                'counted_at' => $data['counted_at'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($filteredItems as $item) {
                $product = Product::findOrFail($item['product_id']);
                $balance = StockBalance::where('product_id', $product->id)
                    ->where('location_id', $inventory->location_id)
                    ->first();

                $systemQty = (float) ($balance?->quantity ?? 0);
                $countedQty = (float) $item['counted_quantity'];
                $diff = $countedQty - $systemQty;
                $unitCost = (float) ($balance?->avg_cost_local ?? $product->avg_cost_local);
                $unitSale = (float) $product->sale_price_local;

                InventoryCountItem::create([
                    'inventory_count_id' => $inventory->id,
                    'product_id' => $product->id,
                    'counted_quantity' => $countedQty,
                    'system_quantity' => $systemQty,
                    'difference' => $diff,
                    'unit_cost_local' => $unitCost,
                    'unit_sale_price_local' => $unitSale,
                ]);

                if ($diff !== 0.0) {
                    $stockService->recordMovement([
                        'product_id' => $product->id,
                        'from_location_id' => $diff < 0 ? $inventory->location_id : null,
                        'to_location_id' => $diff > 0 ? $inventory->location_id : null,
                        'quantity' => abs($diff),
                        'unit_cost_local' => $unitCost,
                        'unit_sale_price_local' => $unitSale,
                        'type' => $diff > 0 ? 'adjustment_in' : 'adjustment_out',
                        'reference_type' => InventoryCount::class,
                        'reference_id' => $inventory->id,
                        'occurred_at' => $inventory->counted_at ?? now(),
                    ]);
                }
            }
        });

        $this->redirectRoute('inventory-counts.index');
    }

    public function render()
    {
        $locations = LocationAccess::restrictLocations(StockLocation::query()->orderBy('name'))->get();
        $products = Product::orderBy('name')->get();
        $canSelectAnyLocation = LocationAccess::hasGlobalAccess();

        return view('livewire.inventory-counts.create', compact('locations', 'products', 'canSelectAnyLocation'))
            ->layout('layouts.app');
    }
}
