<?php

namespace App\Livewire\Company;

use App\Models\CompanySetting;
use Livewire\Component;
use Livewire\WithFileUploads;

class Settings extends Component
{
    use WithFileUploads;

    public CompanySetting $company;

    public string $name = '';
    public string $legal_name = '';
    public string $tax_id = '';
    public string $phone = '';
    public string $email = '';
    public string $address = '';
    public string $currency = 'CDF';
    public string $invoice_footer = '';
    public $logo;

    public function mount(): void
    {
        $this->company = CompanySetting::firstOrCreate(['id' => 1], ['name' => 'Entreprise']);
        $this->name = (string) ($this->company->name ?? 'Entreprise');
        $this->legal_name = (string) ($this->company->legal_name ?? '');
        $this->tax_id = (string) ($this->company->tax_id ?? '');
        $this->phone = (string) ($this->company->phone ?? '');
        $this->email = (string) ($this->company->email ?? '');
        $this->address = (string) ($this->company->address ?? '');
        $this->currency = (string) ($this->company->currency ?? 'CDF');
        $this->invoice_footer = (string) ($this->company->invoice_footer ?? '');
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'tax_id' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'currency' => ['required', 'string', 'max:10'],
            'invoice_footer' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($this->logo) {
            $path = $this->logo->store('company');
            $data['logo_path'] = $path;
        }

        $data['legal_name'] = $data['legal_name'] ?: null;
        $data['tax_id'] = $data['tax_id'] ?: null;
        $data['phone'] = $data['phone'] ?: null;
        $data['email'] = $data['email'] ?: null;
        $data['address'] = $data['address'] ?: null;
        $data['invoice_footer'] = $data['invoice_footer'] ?: null;

        $this->company->update($data);
        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.company.settings')
            ->layout('layouts.app');
    }
}
