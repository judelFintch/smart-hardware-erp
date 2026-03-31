<?php

namespace App\Livewire\Suppliers;

use App\Livewire\Concerns\ConfirmsDeletionWithSecretCode;
use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use ConfirmsDeletionWithSecretCode, WithPagination;

    public int $perPage = 15;

    protected function performDelete(int $supplierId): void
    {
        Supplier::whereKey($supplierId)->delete();
    }

    public function render()
    {
        $suppliers = Supplier::orderBy('name')->paginate($this->perPage);

        return view('livewire.suppliers.index', compact('suppliers'))
            ->layout('layouts.app');
    }
}
