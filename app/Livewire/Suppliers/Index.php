<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use Livewire\Component;

class Index extends Component
{
    public function delete(int $supplierId): void
    {
        Supplier::whereKey($supplierId)->delete();
    }

    public function render()
    {
        $suppliers = Supplier::orderBy('name')->get();

        return view('livewire.suppliers.index', compact('suppliers'))
            ->layout('layouts.app');
    }
}
