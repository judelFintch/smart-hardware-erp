<?php

namespace App\Livewire\Customers;

use App\Livewire\Concerns\ConfirmsDeletionWithSecretCode;
use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use ConfirmsDeletionWithSecretCode, WithPagination;

    public int $perPage = 15;

    protected function performDelete(int $customerId): void
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
