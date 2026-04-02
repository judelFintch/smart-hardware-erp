<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\StockMovement;
use App\Support\LocationAccess;
use Livewire\Component;
use Livewire\WithPagination;

class StockCard extends Component
{
    use WithPagination;

    public Product $product;
    public int $perPage = 20;

    public function mount(Product $product): void
    {
        $this->product = $product->load(['unit']);
    }

    public function render()
    {
        $this->product->load([
            'unit',
            'stockBalances' => fn ($query) => LocationAccess::filterByLocation($query, 'location_id'),
            'stockBalances.location',
        ]);

        $balances = $this->product->stockBalances
            ->sortBy([
                ['quantity', 'desc'],
                ['location.name', 'asc'],
            ])
            ->values();

        $totalStock = (float) $balances->sum('quantity');
        $stockValue = (float) $balances->sum(fn ($balance) => (float) $balance->quantity * (float) $balance->avg_cost_local);
        $activeLocations = $balances->where('quantity', '>', 0)->count();

        $movements = StockMovement::query()
            ->with(['fromLocation', 'toLocation'])
            ->where('product_id', $this->product->id)
            ->when(!LocationAccess::hasGlobalAccess(), fn ($query) => LocationAccess::filterByLocation($query, ['from_location_id', 'to_location_id']))
            ->orderByDesc('occurred_at')
            ->orderByDesc('id')
            ->paginate($this->perPage);

        $lastMovementAt = StockMovement::query()
            ->where('product_id', $this->product->id)
            ->when(!LocationAccess::hasGlobalAccess(), fn ($query) => LocationAccess::filterByLocation($query, ['from_location_id', 'to_location_id']))
            ->orderByDesc('occurred_at')
            ->orderByDesc('id')
            ->first()?->occurred_at;

        $runningStock = $totalStock;
        $movements->getCollection()->transform(function (StockMovement $movement) use (&$runningStock) {
            $delta = $this->movementDelta($movement);
            $movement->stock_delta = $delta;
            $movement->stock_after = $runningStock;
            $runningStock -= $delta;

            return $movement;
        });

        return view('livewire.products.stock-card', compact(
            'balances',
            'movements',
            'totalStock',
            'stockValue',
            'activeLocations',
            'lastMovementAt'
        ))->layout('layouts.app');
    }

    public function formatMovementType(string $type): string
    {
        return match ($type) {
            'purchase_in' => 'Entrée achat',
            'sale_out' => 'Sortie vente',
            'transfer_in' => 'Transfert',
            'adjustment_in' => 'Ajustement +',
            'adjustment_out' => 'Ajustement -',
            default => $type,
        };
    }

    public function formatReference(?string $referenceType, ?int $referenceId): string
    {
        if (!$referenceType || !$referenceId) {
            return '—';
        }

        $label = str_contains($referenceType, 'PurchaseOrder')
            ? 'Achat'
            : (str_contains($referenceType, 'Sale') ? 'Vente' : ucfirst((string) str_replace('_', ' ', class_basename($referenceType))));

        return "{$label} #{$referenceId}";
    }

    private function movementDelta(StockMovement $movement): float
    {
        return match ($movement->type) {
            'purchase_in', 'adjustment_in' => (float) $movement->quantity,
            'sale_out', 'adjustment_out' => -1 * (float) $movement->quantity,
            default => 0.0,
        };
    }
}
