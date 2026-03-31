<?php

namespace App\Livewire\Company;

use App\Models\CompanySetting;
use App\Services\NotificationService;
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
    public string $currency_symbol = 'FC';
    public string $timezone = 'Africa/Lubumbashi';
    public string $date_format = 'd/m/Y';
    public string $purchase_prefix = 'ACH';
    public string $sale_prefix = 'VTE';
    public float $tax_rate = 0;
    public float $low_stock_threshold = 0;
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
        $this->currency_symbol = (string) ($this->company->currency_symbol ?? 'FC');
        $this->timezone = (string) ($this->company->timezone ?? config('app.timezone', 'Africa/Lubumbashi'));
        $this->date_format = (string) ($this->company->date_format ?? 'd/m/Y');
        $this->purchase_prefix = (string) ($this->company->purchase_prefix ?? 'ACH');
        $this->sale_prefix = (string) ($this->company->sale_prefix ?? 'VTE');
        $this->tax_rate = (float) ($this->company->tax_rate ?? 0);
        $this->low_stock_threshold = (float) ($this->company->low_stock_threshold ?? 0);
        $this->invoice_footer = (string) ($this->company->invoice_footer ?? '');
    }

    public function save(NotificationService $notifications): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'tax_id' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'currency' => ['required', 'string', 'max:10'],
            'currency_symbol' => ['required', 'string', 'max:10'],
            'timezone' => ['required', 'timezone'],
            'date_format' => ['required', 'string', 'max:20'],
            'purchase_prefix' => ['required', 'string', 'max:20'],
            'sale_prefix' => ['required', 'string', 'max:20'],
            'tax_rate' => ['nullable', 'numeric', 'min:0'],
            'low_stock_threshold' => ['nullable', 'numeric', 'min:0'],
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
        $data['tax_rate'] = (float) ($data['tax_rate'] ?? 0);
        $data['low_stock_threshold'] = (float) ($data['low_stock_threshold'] ?? 0);
        $data['invoice_footer'] = $data['invoice_footer'] ?: null;

        $this->company->update($data);
        $notifications->notifyManagers(
            'Paramètres entreprise mis à jour',
            'La configuration générale de l’entreprise a été modifiée.',
            'success',
            route('company.settings'),
            'company-settings-updated'
        );
        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.company.settings')
            ->layout('layouts.app');
    }
}
