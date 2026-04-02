<?php

namespace App\Livewire\InventoryCounts;

use App\Models\InventoryCount;
use App\Models\InventoryCountItem;
use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockLocation;
use App\Services\StockService;
use App\Support\LocationAccess;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{
    public ?int $location_id = null;
    public ?string $counted_at = null;
    public string $notes = '';
    public array $items = [];
    public string $search = '';
    public bool $showOnlyCounted = false;

    public function mount(): void
    {
        $this->location_id = LocationAccess::assignedLocationId();
        $this->loadItemsForLocation();
    }

    public function updatedLocationId(): void
    {
        $this->loadItemsForLocation();
    }

    public function loadItemsForLocation(): void
    {
        if (!$this->location_id) {
            $this->items = [];

            return;
        }

        LocationAccess::ensureLocationAllowed((int) $this->location_id);

        $balances = StockBalance::query()
            ->with('product')
            ->where('location_id', $this->location_id)
            ->orderByDesc('quantity')
            ->get()
            ->sortBy(fn (StockBalance $balance) => $balance->product?->name ?? '')
            ->values();

        $this->items = $balances->map(function (StockBalance $balance) {
            return [
                'product_id' => $balance->product_id,
                'product_name' => $balance->product?->name ?? 'Article',
                'product_sku' => $balance->product?->sku ?? null,
                'system_quantity' => (float) $balance->quantity,
                'counted_quantity' => null,
            ];
        })->all();
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
            'items.*.system_quantity' => ['nullable', 'numeric', 'min:0'],
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
                'created_by' => auth()->id(),
            ]);

            foreach ($filteredItems as $item) {
                $product = Product::findOrFail($item['product_id']);
                $balance = StockBalance::where('product_id', $product->id)
                    ->where('location_id', $inventory->location_id)
                    ->first();

                $systemQty = (float) ($item['system_quantity'] ?? $balance?->quantity ?? 0);
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
        $canSelectAnyLocation = LocationAccess::hasGlobalAccess();
        $visibleItems = $this->visibleItems();
        $countedItems = collect($this->items)->filter(fn (array $item) => $item['counted_quantity'] !== null && $item['counted_quantity'] !== '')->count();
        $differencesCount = collect($this->items)->filter(function (array $item) {
            if ($item['counted_quantity'] === null || $item['counted_quantity'] === '') {
                return false;
            }

            return (float) $item['counted_quantity'] !== (float) ($item['system_quantity'] ?? 0);
        })->count();

        return view('livewire.inventory-counts.create', compact('locations', 'canSelectAnyLocation', 'visibleItems', 'countedItems', 'differencesCount'))
            ->layout('layouts.app');
    }

    private function visibleItems(): Collection
    {
        $query = mb_strtolower(trim($this->search));

        return collect($this->items)
            ->filter(function (array $item) use ($query) {
                if ($this->showOnlyCounted && ($item['counted_quantity'] === null || $item['counted_quantity'] === '')) {
                    return false;
                }

                if ($query === '') {
                    return true;
                }

                $haystack = mb_strtolower(
                    trim(($item['product_name'] ?? '') . ' ' . ($item['product_sku'] ?? ''))
                );

                return str_contains($haystack, $query);
            });
    }
}
