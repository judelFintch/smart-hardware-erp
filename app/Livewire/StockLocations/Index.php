<?php

namespace App\Livewire\StockLocations;

use App\Models\StockLocation;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public int $perPage = 15;

    public function delete(int $locationId): void
    {
        StockLocation::whereKey($locationId)->delete();
    }

    public function render()
    {
        $locations = StockLocation::orderBy('name')->paginate($this->perPage);

        return view('livewire.stock-locations.index', compact('locations'))
            ->layout('layouts.app');
    }
}
