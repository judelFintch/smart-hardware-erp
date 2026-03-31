<?php

namespace App\Livewire\StockLocations;

use App\Models\StockLocation;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Form extends Component
{
    public ?StockLocation $location = null;
    public string $code = '';
    public string $name = '';
    public string $notes = '';
    public bool $is_default_sale = false;

    public function mount(?StockLocation $stockLocation = null): void
    {
        if ($stockLocation && $stockLocation->exists) {
            $this->location = $stockLocation;
            $this->code = $stockLocation->code;
            $this->name = $stockLocation->name;
            $this->notes = (string) $stockLocation->notes;
            $this->is_default_sale = (bool) $stockLocation->is_default_sale;
        }
    }

    public function save(): void
    {
        $data = $this->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('stock_locations', 'code')->ignore($this->location?->id)->whereNull('deleted_at')],
            'name' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'is_default_sale' => ['boolean'],
        ]);

        DB::transaction(function () use ($data) {
            if ($data['is_default_sale']) {
                StockLocation::query()
                    ->when($this->location, fn ($query) => $query->whereKeyNot($this->location->id))
                    ->update(['is_default_sale' => false]);
            }

            if ($this->location) {
                $this->location->update($data);
                return;
            }

            StockLocation::create($data);
        });

        $this->redirectRoute('stock-locations.index');
    }

    public function render()
    {
        $title = $this->location ? 'Modifier Magasin' : 'Nouveau Magasin/Dépôt';

        return view('livewire.stock-locations.form', compact('title'))
            ->layout('layouts.app');
    }
}
