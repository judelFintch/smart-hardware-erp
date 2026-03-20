<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\Unit;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Form extends Component
{
    public ?Product $product = null;
    public string $sku = '';
    public string $name = '';
    public string $barcode = '';
    public ?int $unit_id = null;
    public string $description = '';
    public float $sale_margin_percent = 0;
    public float $reorder_level = 0;
    public bool $is_active = true;

    public function mount(?Product $product = null): void
    {
        if ($product && $product->exists) {
            $this->product = $product;
            $this->sku = $product->sku;
            $this->name = $product->name;
            $this->barcode = (string) $product->barcode;
            $this->unit_id = $product->unit_id;
            $this->description = (string) $product->description;
            $this->sale_margin_percent = (float) $product->sale_margin_percent;
            $this->reorder_level = (float) $product->reorder_level;
            $this->is_active = (bool) $product->is_active;
        }
    }

    public function save(): void
    {
        $data = $this->validate([
            'sku' => ['required', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($this->product?->id)->whereNull('deleted_at')],
            'barcode' => ['nullable', 'string', 'max:255', Rule::unique('products', 'barcode')->ignore($this->product?->id)->whereNull('deleted_at')],
            'name' => ['required', 'string', 'max:255'],
            'unit_id' => ['required', 'exists:units,id'],
            'description' => ['nullable', 'string'],
            'sale_margin_percent' => ['nullable', 'numeric', 'min:0'],
            'reorder_level' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        if ($this->product) {
            $salePrice = $this->product->avg_cost_local > 0
                ? $this->product->avg_cost_local * (1 + (((float) $data['sale_margin_percent']) / 100))
                : 0;
            $this->product->update(array_merge($data, ['sale_price_local' => $salePrice]));
        } else {
            $data['avg_cost_local'] = 0;
            $data['sale_price_local'] = 0;
            Product::create($data);
        }

        $this->redirectRoute('products.index');
    }

    public function render()
    {
        $title = $this->product ? 'Modifier Article' : 'Nouvel Article';
        $units = Unit::orderBy('name')->get();

        return view('livewire.products.form', compact('title', 'units'))
            ->layout('layouts.app');
    }
}
