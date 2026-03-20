<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Achats</h1>
        <div class="flex gap-2 items-center">
            <form wire:submit.prevent="importCsv" class="flex items-center gap-2">
                <input type="file" wire:model="importFile" class="input bg-white" />
                <button class="btn btn-secondary" type="submit">Importer CSV</button>
            </form>
            @error('importFile') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            <button wire:click="export" class="btn btn-secondary" type="button">Exporter Excel</button>
            <button wire:click="exportPdf" class="btn btn-secondary" type="button">Exporter PDF</button>
            <a href="{{ route('purchases.create') }}" class="btn btn-primary">Nouvel achat</a>
        </div>
    </div>
    <div class="card overflow-hidden">
        @if ($purchases->isEmpty())
            <x-empty-state
                title="Aucun achat"
                description="Créez votre premier achat fournisseur."
                action="Nouvel achat"
                :action-href="route('purchases.create')"
            />
        @else
            <table class="w-full">
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
                            <td class="p-2 flex gap-2">
                                <a href="{{ route('purchases.show', $purchase) }}" class="text-blue-600">Voir</a>
                                <a href="{{ route('purchases.edit', $purchase) }}" class="text-indigo-600">Modifier</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    <div>
        {{ $purchases->links() }}
    </div>
</div>
