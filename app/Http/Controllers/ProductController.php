<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::orderBy('name')->get();

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        return view('products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'sku' => ['required', 'string', 'max:255', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'sale_margin_percent' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['unit'] = $data['unit'] ?? 'pcs';
        $data['avg_cost_local'] = 0;
        $margin = (float) ($data['sale_margin_percent'] ?? 0);
        $data['sale_price_local'] = 0;
        if ($data['avg_cost_local'] > 0) {
            $data['sale_price_local'] = $data['avg_cost_local'] * (1 + ($margin / 100));
        }

        Product::create($data);

        return redirect()->route('products.index');
    }

    public function edit(Product $product): View
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'sku' => ['required', 'string', 'max:255', 'unique:products,sku,' . $product->id],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'sale_margin_percent' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['unit'] = $data['unit'] ?? 'pcs';
        $margin = (float) ($data['sale_margin_percent'] ?? $product->sale_margin_percent);
        $data['sale_price_local'] = $product->avg_cost_local > 0
            ? $product->avg_cost_local * (1 + ($margin / 100))
            : 0;

        $product->update($data);

        return redirect()->route('products.index');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index');
    }
}
