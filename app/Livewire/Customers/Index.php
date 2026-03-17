<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public int $perPage = 15;

    public function delete(int $customerId): void
    {
        Customer::whereKey($customerId)->delete();
    }

    public function render()
    {
        $customers = Customer::orderBy('name')->paginate($this->perPage);

        return view('livewire.customers.index', compact('customers'))
            ->layout('layouts.app');
    }
}
