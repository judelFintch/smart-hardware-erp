<?php

namespace App\Livewire\Units;

use App\Livewire\Concerns\ConfirmsDeletionWithSecretCode;
use App\Models\Unit;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use ConfirmsDeletionWithSecretCode, WithPagination;

    public int $perPage = 15;

    protected function performDelete(int $unitId): void
    {
        Unit::whereKey($unitId)->delete();
    }

    public function render()
    {
        $units = Unit::orderBy('type')->orderBy('name')->paginate($this->perPage);

        return view('livewire.units.index', compact('units'))
            ->layout('layouts.app');
    }
}
