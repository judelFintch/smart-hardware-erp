<div>
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Achats</h1>
        <div class="flex gap-2">
            <button wire:click="export" class="px-3 py-2 bg-gray-700 text-white rounded" type="button">Exporter CSV</button>
            <a href="{{ route('purchases.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">Nouvel achat</a>
        </div>
    </div>
    <table class="w-full bg-white shadow rounded">
        <thead>
            <tr class="text-left border-b">
                <th class="p-2">ID</th>
                <th class="p-2">Fournisseur</th>
                <th class="p-2">Type</th>
                <th class="p-2">Statut</th>
                <th class="p-2">Total</th>
                <th class="p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchases as $purchase)
                <tr class="border-b">
                    <td class="p-2">{{ $purchase->id }}</td>
                    <td class="p-2">{{ $purchase->supplier->name }}</td>
                    <td class="p-2">{{ $purchase->type }}</td>
                    <td class="p-2">{{ $purchase->status }}</td>
                    <td class="p-2">{{ number_format($purchase->total_cost_local, 2) }}</td>
                    <td class="p-2">
                        <a href="{{ route('purchases.show', $purchase) }}" class="text-blue-600">Voir</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
