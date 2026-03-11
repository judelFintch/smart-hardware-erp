<?php

namespace App\Livewire\Units;

use App\Models\Unit;
use Livewire\Component;

class Index extends Component
{
    public function delete(int $unitId): void
    {
        Unit::whereKey($unitId)->delete();
    }

    public function render()
    {
        $units = Unit::orderBy('type')->orderBy('name')->get();

        return view('livewire.units.index', compact('units'))
            ->layout('layouts.app');
    }
}
