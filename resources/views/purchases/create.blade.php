@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold mb-4">Nouvel Achat</h1>
    <form method="POST" action="{{ route('purchases.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block">Fournisseur</label>
            <select name="supplier_id" class="border p-2 w-full" required>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->name }} ({{ $supplier->type }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block">Type</label>
            <select name="type" class="border p-2 w-full" required>
                <option value="local">Local</option>
                <option value="foreign">Étranger</option>
            </select>
        </div>
        <div>
            <label class="block">Statut</label>
            <select name="status" class="border p-2 w-full" required>
                <option value="en_cours">En cours</option>
                <option value="en_transit">En transit</option>
                <option value="receptionnee">Réceptionnée</option>
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block">Commande le</label>
                <input name="ordered_at" type="date" class="border p-2 w-full">
            </div>
            <div>
                <label class="block">En transit le</label>
                <input name="in_transit_at" type="date" class="border p-2 w-full">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block">Réception le</label>
                <input name="received_at" type="date" class="border p-2 w-full">
            </div>
            <div>
                <label class="block">Référence</label>
                <input name="reference" class="border p-2 w-full">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block">Devise</label>
                <input name="currency" class="border p-2 w-full" value="CDF">
            </div>
            <div>
                <label class="block">Taux de change</label>
                <input name="exchange_rate" type="number" step="0.000001" class="border p-2 w-full" value="1">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block">Frais accessoires (local)</label>
                <input name="accessory_fees_local" type="number" step="0.01" class="border p-2 w-full" value="0">
            </div>
            <div>
                <label class="block">Frais transport (local)</label>
                <input name="transport_fees_local" type="number" step="0.01" class="border p-2 w-full" value="0">
            </div>
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
                        <input name="items[{{ $index }}][unit_price]" type="number" step="0.01" class="border p-2" placeholder="Prix unitaire">
                    </div>
                @endforeach
            </div>
        </div>

        <div>
            <label class="block">Notes</label>
            <textarea name="notes" class="border p-2 w-full"></textarea>
        </div>

        <button class="px-3 py-2 bg-blue-600 text-white rounded" type="submit">Enregistrer</button>
    </form>
@endsection
