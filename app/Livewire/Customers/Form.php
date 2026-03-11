<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Livewire\Component;

class Form extends Component
{
    public ?Customer $customer = null;
    public string $name = '';
    public string $phone = '';
    public string $email = '';
    public string $address = '';
    public string $notes = '';

    public function mount(?Customer $customer = null): void
    {
        if ($customer && $customer->exists) {
            $this->customer = $customer;
            $this->name = $customer->name;
            $this->phone = (string) $customer->phone;
            $this->email = (string) $customer->email;
            $this->address = (string) $customer->address;
            $this->notes = (string) $customer->notes;
        }
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($this->customer) {
            $this->customer->update($data);
        } else {
            Customer::create($data);
        }

        $this->redirectRoute('customers.index');
    }

    public function render()
    {
        $title = $this->customer ? 'Modifier Client' : 'Nouveau Client';

        return view('livewire.customers.form', compact('title'))
            ->layout('layouts.app');
    }
}
