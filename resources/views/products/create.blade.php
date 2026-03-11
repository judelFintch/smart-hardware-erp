@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold mb-4">Nouvel Article</h1>
    <form method="POST" action="{{ route('products.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block">SKU</label>
            <input name="sku" class="border p-2 w-full" required>
        </div>
        <div>
            <label class="block">Nom</label>
            <input name="name" class="border p-2 w-full" required>
        </div>
        <div>
            <label class="block">Unité</label>
            <input name="unit" class="border p-2 w-full" value="pcs">
        </div>
        <div>
            <label class="block">Marge (%)</label>
            <input name="sale_margin_percent" type="number" step="0.01" class="border p-2 w-full" value="0">
        </div>
        <div>
            <label class="block">Description</label>
            <textarea name="description" class="border p-2 w-full"></textarea>
        </div>
        <button class="px-3 py-2 bg-blue-600 text-white rounded" type="submit">Enregistrer</button>
    </form>
@endsection
