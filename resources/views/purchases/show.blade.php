@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold mb-4">Achat #{{ $purchaseOrder->id }}</h1>

    <div class="bg-white shadow rounded p-4 mb-4">
        <p><strong>Fournisseur:</strong> {{ $purchaseOrder->supplier->name }}</p>
        <p><strong>Type:</strong> {{ $purchaseOrder->type }}</p>
        <p><strong>Statut:</strong> {{ $purchaseOrder->status }}</p>
        <p><strong>Total:</strong> {{ number_format($purchaseOrder->total_cost_local, 2) }}</p>
    </div>

    <h2 class="text-lg font-semibold mb-2">Articles</h2>
    <table class="w-full bg-white shadow rounded mb-6">
        <thead>
            <tr class="text-left border-b">
                <th class="p-2">Article</th>
                <th class="p-2">Quantité</th>
                <th class="p-2">Coût unitaire</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchaseOrder->items as $item)
                <tr class="border-b">
                    <td class="p-2">{{ $item->product->name }}</td>
                    <td class="p-2">{{ $item->quantity }}</td>
                    <td class="p-2">{{ number_format($item->unit_cost_local, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($purchaseOrder->type === 'foreign')
        <h2 class="text-lg font-semibold mb-2">Transferts</h2>
        <form method="POST" action="{{ route('purchases.transfers.store', $purchaseOrder) }}" class="space-y-2 mb-4">
            @csrf
            <div class="grid grid-cols-2 gap-2">
                <input name="amount_foreign" type="number" step="0.01" class="border p-2" placeholder="Montant devise">
                <input name="amount_local" type="number" step="0.01" class="border p-2" placeholder="Montant local">
            </div>
            <div class="grid grid-cols-2 gap-2">
                <input name="paid_at" type="date" class="border p-2">
                <input name="reference" class="border p-2" placeholder="Référence">
            </div>
            <textarea name="notes" class="border p-2 w-full" placeholder="Notes"></textarea>
            <button class="px-3 py-2 bg-blue-600 text-white rounded" type="submit">Ajouter transfert</button>
        </form>
    @endif

    @if ($purchaseOrder->status !== 'receptionnee')
        <form method="POST" action="{{ route('purchases.receive', $purchaseOrder) }}">
            @csrf
            <button class="px-3 py-2 bg-green-600 text-white rounded" type="submit">Marquer réception & stocker</button>
        </form>
    @endif
@endsection
