@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold mb-4">Modifier Article</h1>
    <form method="POST" action="{{ route('products.update', $product) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block">SKU</label>
            <input name="sku" class="border p-2 w-full" value="{{ $product->sku }}" required>
        </div>
        <div>
            <label class="block">Nom</label>
            <input name="name" class="border p-2 w-full" value="{{ $product->name }}" required>
        </div>
        <div>
            <label class="block">Unité</label>
            <input name="unit" class="border p-2 w-full" value="{{ $product->unit }}">
        </div>
        <div>
            <label class="block">Marge (%)</label>
            <input name="sale_margin_percent" type="number" step="0.01" class="border p-2 w-full" value="{{ $product->sale_margin_percent }}">
        </div>
        <div>
            <label class="block">Description</label>
            <textarea name="description" class="border p-2 w-full">{{ $product->description }}</textarea>
        </div>
        <button class="px-3 py-2 bg-blue-600 text-white rounded" type="submit">Mettre à jour</button>
    </form>
@endsection
