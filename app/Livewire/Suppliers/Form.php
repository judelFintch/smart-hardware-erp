<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use Livewire\Component;

class Form extends Component
{
    public ?Supplier $supplier = null;
    public string $name = '';
    public string $type = 'local';
    public string $phone = '';
    public string $email = '';
    public string $address = '';
    public string $notes = '';

    public function mount(?Supplier $supplier = null): void
    {
        if ($supplier && $supplier->exists) {
            $this->supplier = $supplier;
            $this->name = $supplier->name;
            $this->type = $supplier->type;
            $this->phone = (string) $supplier->phone;
            $this->email = (string) $supplier->email;
            $this->address = (string) $supplier->address;
            $this->notes = (string) $supplier->notes;
        }
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:local,foreign'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($this->supplier) {
            $this->supplier->update($data);
        } else {
            Supplier::create($data);
        }

        $this->redirectRoute('suppliers.index');
    }

    public function render()
    {
        $title = $this->supplier ? 'Modifier Fournisseur' : 'Nouveau Fournisseur';

        return view('livewire.suppliers.form', compact('title'))
            ->layout('layouts.app');
    }
}
