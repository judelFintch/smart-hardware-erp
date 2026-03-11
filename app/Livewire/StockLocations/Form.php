<?php

namespace App\Livewire\StockLocations;

use App\Models\StockLocation;
use Livewire\Component;

class Form extends Component
{
    public ?StockLocation $location = null;
    public string $code = '';
    public string $name = '';
    public string $notes = '';

    public function mount(?StockLocation $stockLocation = null): void
    {
        if ($stockLocation && $stockLocation->exists) {
            $this->location = $stockLocation;
            $this->code = $stockLocation->code;
            $this->name = $stockLocation->name;
            $this->notes = (string) $stockLocation->notes;
        }
    }

    public function save(): void
    {
        $data = $this->validate([
            'code' => ['required', 'string', 'max:50', 'unique:stock_locations,code,' . ($this->location?->id ?? 'NULL')],
            'name' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($this->location) {
            $this->location->update($data);
        } else {
            StockLocation::create($data);
        }

        $this->redirectRoute('stock-locations.index');
    }

    public function render()
    {
        $title = $this->location ? 'Modifier Magasin' : 'Nouveau Magasin/Dépôt';

        return view('livewire.stock-locations.form', compact('title'))
            ->layout('layouts.app');
    }
}
