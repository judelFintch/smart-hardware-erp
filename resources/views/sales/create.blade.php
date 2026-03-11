@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold mb-4">Nouvelle Vente</h1>
    <form method="POST" action="{{ route('sales.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block">Type</label>
            <select name="type" class="border p-2 w-full" required>
                <option value="cash">Comptant</option>
                <option value="credit">Crédit</option>
            </select>
        </div>
        <div>
            <label class="block">Client (crédit)</label>
            <select name="customer_id" class="border p-2 w-full">
                <option value="">-- Aucun --</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block">Date</label>
            <input name="sold_at" type="datetime-local" class="border p-2 w-full">
        </div>
        <div>
            <h2 class="text-lg font-semibold mb-2">Articles</h2>
            <div class="space-y-2">
                @foreach (range(0,4) as $index)
                    <div class="grid grid-cols-3 gap-2">
                        <select name="items[{{ $index }}][product_id]" class="border p-2">
                            <option value="">-- Article --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <input name="items[{{ $index }}][quantity]" type="number" step="0.001" class="border p-2" placeholder="Quantité">
                        <input name="items[{{ $index }}][discount_amount]" type="number" step="0.01" class="border p-2" placeholder="Réduction">
                    </div>
                @endforeach
            </div>
        </div>
        <button class="px-3 py-2 bg-blue-600 text-white rounded" type="submit">Enregistrer</button>
    </form>
@endsection
