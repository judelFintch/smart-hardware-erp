<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Livewire\Component;

class Index extends Component
{
    public function delete(int $customerId): void
    {
        Customer::whereKey($customerId)->delete();
    }

    public function render()
    {
        $customers = Customer::orderBy('name')->get();

        return view('livewire.customers.index', compact('customers'))
            ->layout('layouts.app');
    }
}
