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
        $this->fill($this->company->only([
            'name',
            'legal_name',
            'tax_id',
            'phone',
            'email',
            'address',
            'currency',
            'invoice_footer',
        ]));
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

        $this->company->update($data);
        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.company.settings')
            ->layout('layouts.app');
    }
}
