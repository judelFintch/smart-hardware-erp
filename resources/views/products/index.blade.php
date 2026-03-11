@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Articles</h1>
        <a href="{{ route('products.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">Nouveau</a>
    </div>
    <table class="w-full bg-white shadow rounded">
        <thead>
            <tr class="text-left border-b">
                <th class="p-2">SKU</th>
                <th class="p-2">Nom</th>
                <th class="p-2">Unité</th>
                <th class="p-2">Coût moyen</th>
                <th class="p-2">Prix vente</th>
                <th class="p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr class="border-b">
                    <td class="p-2">{{ $product->sku }}</td>
                    <td class="p-2">{{ $product->name }}</td>
                    <td class="p-2">{{ $product->unit }}</td>
                    <td class="p-2">{{ number_format($product->avg_cost_local, 2) }}</td>
                    <td class="p-2">{{ number_format($product->sale_price_local, 2) }}</td>
                    <td class="p-2">
                        <a href="{{ route('products.edit', $product) }}" class="text-blue-600">Modifier</a>
                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600" type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
