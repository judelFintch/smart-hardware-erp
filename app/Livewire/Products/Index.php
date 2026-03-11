<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;

class Index extends Component
{
    public function delete(int $productId): void
    {
        Product::whereKey($productId)->delete();
    }

    public function render()
    {
        $products = Product::orderBy('name')->get();

        return view('livewire.products.index', compact('products'))
            ->layout('layouts.app');
    }
}
