@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold mb-4">Vente #{{ $sale->id }}</h1>

    <div class="bg-white shadow rounded p-4 mb-4">
        <p><strong>Client:</strong> {{ $sale->customer?->name ?? '—' }}</p>
        <p><strong>Type:</strong> {{ $sale->type }}</p>
        <p><strong>Statut:</strong> {{ $sale->status }}</p>
        <p><strong>Total:</strong> {{ number_format($sale->total_amount, 2) }}</p>
    </div>

    <h2 class="text-lg font-semibold mb-2">Articles</h2>
    <table class="w-full bg-white shadow rounded mb-6">
        <thead>
            <tr class="text-left border-b">
                <th class="p-2">Article</th>
                <th class="p-2">Quantité</th>
                <th class="p-2">Prix</th>
                <th class="p-2">Réduction</th>
                <th class="p-2">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sale->items as $item)
                <tr class="border-b">
                    <td class="p-2">{{ $item->product->name }}</td>
                    <td class="p-2">{{ $item->quantity }}</td>
                    <td class="p-2">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="p-2">{{ number_format($item->discount_amount, 2) }}</td>
                    <td class="p-2">{{ number_format($item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2 class="text-lg font-semibold mb-2">Retour / Échange</h2>
    <form method="POST" action="{{ route('sales.returns.store', $sale) }}" class="space-y-2 mb-6">
        @csrf
        <div class="grid grid-cols-2 gap-2">
            <select name="product_id" class="border p-2" required>
                @foreach ($sale->items->where('quantity', '>', 0) as $item)
                    <option value="{{ $item->product_id }}">{{ $item->product->name }}</option>
                @endforeach
            </select>
            <input name="quantity" type="number" step="0.001" class="border p-2" placeholder="Quantité retournée" required>
        </div>
        <button class="px-3 py-2 bg-orange-600 text-white rounded" type="submit">Enregistrer retour</button>
    </form>

    @if ($sale->type === 'credit')
        <h2 class="text-lg font-semibold mb-2">Paiements</h2>
        <form method="POST" action="{{ route('sales.payments.store', $sale) }}" class="space-y-2 mb-4">
            @csrf
            <div class="grid grid-cols-2 gap-2">
                <input name="amount" type="number" step="0.01" class="border p-2" placeholder="Montant" required>
                <input name="paid_at" type="date" class="border p-2">
            </div>
            <div class="grid grid-cols-2 gap-2">
                <input name="method" class="border p-2" placeholder="Méthode">
                <input name="reference" class="border p-2" placeholder="Référence">
            </div>
            <textarea name="notes" class="border p-2 w-full" placeholder="Notes"></textarea>
            <button class="px-3 py-2 bg-blue-600 text-white rounded" type="submit">Ajouter paiement</button>
        </form>

        <table class="w-full bg-white shadow rounded">
            <thead>
                <tr class="text-left border-b">
                    <th class="p-2">Date</th>
                    <th class="p-2">Montant</th>
                    <th class="p-2">Méthode</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->payments as $payment)
                    <tr class="border-b">
                        <td class="p-2">{{ $payment->paid_at }}</td>
                        <td class="p-2">{{ number_format($payment->amount, 2) }}</td>
                        <td class="p-2">{{ $payment->method }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
