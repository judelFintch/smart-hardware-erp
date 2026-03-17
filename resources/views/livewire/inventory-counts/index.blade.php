<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold">Inventaires</h1>
            <p class="text-sm text-slate-500">Historique des inventaires réalisés.</p>
        </div>
        <div class="flex gap-2">
            <form wire:submit.prevent="importCsv" class="flex items-center gap-2">
                <input type="file" wire:model="importFile" class="input bg-white" />
                <button class="btn btn-secondary" type="submit">Importer CSV</button>
            </form>
            @error('importFile') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            <button wire:click="export" class="btn btn-secondary" type="button">Exporter Excel</button>
            <button wire:click="exportPdf" class="btn btn-secondary" type="button">Exporter PDF</button>
            <a href="{{ route('inventory-counts.create') }}" class="btn btn-primary" wire:navigate>Nouvel inventaire</a>
        </div>
    </div>

    @if ($summary)
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="text-sm text-slate-500">Rapport inventaire</div>
                    <div class="text-lg font-semibold">Dernier inventaire #{{ $summary['count']->id }}</div>
                </div>
                <div class="text-sm text-slate-400">{{ $summary['count']->counted_at?->format('d/m/Y') }}</div>
            </div>
            <div class="card-body grid gap-4 md:grid-cols-2">
                <div>
                    <div class="text-sm text-slate-500">Manquants (quantité)</div>
                    <div class="text-xl font-semibold">{{ number_format($summary['missing_qty'], 3) }}</div>
                    <div class="text-xs text-slate-400">Valeur: {{ number_format($summary['missing_value'], 2) }}</div>
                </div>
                <div>
                    <div class="text-sm text-slate-500">Surplus (quantité)</div>
                    <div class="text-xl font-semibold">{{ number_format($summary['surplus_qty'], 3) }}</div>
                    <div class="text-xs text-slate-400">Valeur: {{ number_format($summary['surplus_value'], 2) }}</div>
                </div>
            </div>
        </div>
    @endif

    <div class="card overflow-hidden">
        @if ($counts->isEmpty())
            <x-empty-state
                title="Aucun inventaire"
                description="Créez un inventaire pour comparer le stock."
                action="Nouvel inventaire"
                :action-href="route('inventory-counts.create')"
            />
        @else
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b">
                        <th class="p-3">ID</th>
                        <th class="p-3">Magasin</th>
                        <th class="p-3">Date</th>
                        <th class="p-3">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($counts as $count)
                        <tr class="border-b">
                            <td class="p-3">{{ $count->id }}</td>
                            <td class="p-3">{{ $count->location?->name }}</td>
                            <td class="p-3">{{ $count->counted_at?->format('d/m/Y') }}</td>
                            <td class="p-3 text-slate-500">{{ $count->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    <div>
        {{ $counts->links() }}
    </div>
</div>
