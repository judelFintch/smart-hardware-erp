<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Ventes</h1>
        <div class="flex gap-2">
            <button wire:click="export" class="btn btn-secondary" type="button">Exporter Excel</button>
            <button wire:click="exportPdf" class="btn btn-secondary" type="button">Exporter PDF</button>
            <a href="{{ route('sales.create') }}" class="btn btn-primary">Nouvelle vente</a>
        </div>
    </div>
    <div class="card overflow-hidden">
        @if ($sales->isEmpty())
            <x-empty-state
                title="Aucune vente"
                description="Enregistrez votre première vente."
                action="Nouvelle vente"
                :action-href="route('sales.create')"
            />
        @else
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b">
                        <th class="p-2">ID</th>
                        <th class="p-2">Client</th>
                        <th class="p-2">Type</th>
                        <th class="p-2">Statut</th>
                        <th class="p-2">Total</th>
                        <th class="p-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sales as $sale)
                        <tr class="border-b">
                            <td class="p-2">{{ $sale->id }}</td>
                            <td class="p-2">{{ $sale->customer?->name }}</td>
                            <td class="p-2">{{ $sale->type }}</td>
                            <td class="p-2">{{ $sale->status }}</td>
                            <td class="p-2">{{ number_format($sale->total_amount, 2) }}</td>
                            <td class="p-2">
                                <a href="{{ route('sales.show', $sale) }}" class="text-blue-600">Voir</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    <div>
        {{ $sales->links() }}
    </div>
</div>
