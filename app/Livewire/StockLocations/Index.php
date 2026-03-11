<?php

namespace App\Livewire\StockLocations;

use App\Models\StockLocation;
use Livewire\Component;

class Index extends Component
{
    public function delete(int $locationId): void
    {
        StockLocation::whereKey($locationId)->delete();
    }

    public function render()
    {
        $locations = StockLocation::orderBy('name')->get();

        return view('livewire.stock-locations.index', compact('locations'))
            ->layout('layouts.app');
    }
}
