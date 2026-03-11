@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold mb-4">Rapport financier</h1>

    <form method="GET" action="{{ route('reports.financial') }}" class="grid grid-cols-3 gap-2 mb-6">
        <input name="start" type="date" class="border p-2" value="{{ $start }}">
        <input name="end" type="date" class="border p-2" value="{{ $end }}">
        <button class="px-3 py-2 bg-blue-600 text-white rounded" type="submit">Filtrer</button>
    </form>

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white shadow rounded p-4">
            <p><strong>Ventes:</strong> {{ number_format($salesTotal, 2) }}</p>
            <p><strong>Coût d'achat vendu:</strong> {{ number_format($cogsTotal, 2) }}</p>
            <p><strong>Dépenses:</strong> {{ number_format($expensesTotal, 2) }}</p>
            <p><strong>Bénéfice:</strong> {{ number_format($profit, 2) }}</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <p><strong>Crédit restant:</strong> {{ number_format($creditOutstanding, 2) }}</p>
        </div>
    </div>

    <h2 class="text-lg font-semibold mb-2">Stocks par lieu</h2>
    @foreach ($stockByLocation as $entry)
        <div class="bg-white shadow rounded p-4 mb-4">
            <h3 class="font-semibold mb-2">{{ $entry['location']->name }}</h3>
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b">
                        <th class="p-2">Article</th>
                        <th class="p-2">Quantité</th>
                        <th class="p-2">Coût moyen</th>
                        <th class="p-2">Prix vente</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($entry['balances'] as $balance)
                        <tr class="border-b">
                            <td class="p-2">{{ $balance->product->name }}</td>
                            <td class="p-2">{{ $balance->quantity }}</td>
                            <td class="p-2">{{ number_format($balance->avg_cost_local, 2) }}</td>
                            <td class="p-2">{{ number_format($balance->sale_price_local, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach
@endsection
