<?php

namespace App\Livewire\Units;

use App\Models\Unit;
use Livewire\Component;

class Form extends Component
{
    public ?Unit $unit = null;
    public string $code = '';
    public string $name = '';
    public string $type = 'piece';

    public function mount(?Unit $unit = null): void
    {
        if ($unit && $unit->exists) {
            $this->unit = $unit;
            $this->code = $unit->code;
            $this->name = $unit->name;
            $this->type = $unit->type;
        }
    }

    public function save(): void
    {
        $data = $this->validate([
            'code' => ['required', 'string', 'max:50', 'unique:units,code,' . ($this->unit?->id ?? 'NULL')],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:piece,weight,volume,other'],
        ]);

        if ($this->unit) {
            $this->unit->update($data);
        } else {
            Unit::create($data);
        }

        $this->redirectRoute('units.index');
    }

    public function render()
    {
        $title = $this->unit ? 'Modifier unité' : 'Nouvelle unité';

        return view('livewire.units.form', compact('title'))
            ->layout('layouts.app');
    }
}
