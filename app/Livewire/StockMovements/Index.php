<?php

namespace App\Livewire\StockMovements;

use App\Models\Product;
use App\Models\StockLocation;
use App\Models\StockMovement;
use App\Support\LocationAccess;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public int $perPage = 20;
    public ?int $product_id = null;
    public ?int $location_id = null;
    public string $type = '';
    public ?string $date_from = null;
    public ?string $date_to = null;

    public function mount(): void
    {
        if (!LocationAccess::hasGlobalAccess()) {
            $this->location_id = LocationAccess::assignedLocationId();
        }
    }

    public function updating($name): void
    {
        if (in_array($name, ['product_id', 'location_id', 'type', 'date_from', 'date_to'], true)) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = StockMovement::with(['product', 'fromLocation', 'toLocation'])
            ->orderByDesc('occurred_at');

        if (!LocationAccess::hasGlobalAccess()) {
            LocationAccess::ensureLocationAllowed($this->location_id);
            LocationAccess::filterByLocation($query, ['from_location_id', 'to_location_id']);
        }

        if ($this->product_id) {
            $query->where('product_id', $this->product_id);
        }

        if ($this->location_id) {
            $query->where(function ($q) {
                $q->where('from_location_id', $this->location_id)
                    ->orWhere('to_location_id', $this->location_id);
            });
        }

        if ($this->type !== '') {
            $query->where('type', $this->type);
        }

        if ($this->date_from) {
            $query->whereDate('occurred_at', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->whereDate('occurred_at', '<=', $this->date_to);
        }

        $movements = $query->paginate($this->perPage);
        $products = Product::orderBy('name')->get();
        $locations = LocationAccess::restrictLocations(StockLocation::query()->orderBy('name'))->get();

        return view('livewire.stock-movements.index', compact('movements', 'products', 'locations'))
            ->layout('layouts.app');
    }
}
