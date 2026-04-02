<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-cyan-50 p-6 shadow-sm">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-2xl">
                <div class="inline-flex items-center rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">
                    Inventaires
                </div>
                <h1 class="mt-3 text-3xl font-semibold text-slate-900">Comptages, écarts et import d’inventaire</h1>
                <p class="mt-2 text-sm text-slate-500">Retrouve l’historique des inventaires et surveille rapidement les manquants ou surplus sur le dernier comptage.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button wire:click="export" class="btn btn-secondary" type="button">Exporter Excel</button>
                <button wire:click="exportPdf" class="btn btn-secondary" type="button">Exporter PDF</button>
                <a href="{{ route('inventory-counts.create') }}" class="btn btn-primary" wire:navigate>Nouvel inventaire</a>
            </div>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <div class="text-sm font-medium text-slate-900">{{ $counts->total() }} inventaire(s) enregistré(s)</div>
                <div class="mt-1 text-sm text-slate-500">Exporte un modèle prérempli avec les articles du lieu, complète la quantité comptée dans Excel, puis réimporte le fichier.</div>
            </div>
            <div class="grid gap-3 lg:grid-cols-2">
                <form wire:submit.prevent="downloadTemplate" class="flex flex-wrap items-center gap-2">
                    <select wire:model="template_location_id" class="input bg-white" @disabled(!$canSelectAnyLocation)>
                        <option value="">Lieu du modèle</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                    <input type="date" wire:model="template_counted_at" class="input bg-white">
                    <button class="btn btn-secondary" type="submit">Modèle Excel</button>
                </form>
                <form wire:submit.prevent="importInventorySheet" class="flex flex-wrap items-center gap-2">
                    <select wire:model="import_location_id" class="input bg-white" @disabled(!$canSelectAnyLocation)>
                        <option value="">Lieu d'import</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                    <input type="file" wire:model="importFile" class="input bg-white" />
                    <button class="btn btn-secondary" type="submit">Importer Excel/CSV</button>
                </form>
                @error('importFile') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                @error('template_location_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                @error('import_location_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="mt-3 rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">
            Colonnes attendues dans le modèle: <span class="font-medium text-slate-900">sku, name, location_code, location_name, system_quantity, counted_quantity, counted_at, unit_cost_local, unit_sale_price_local</span>.
            Remplis surtout <span class="font-medium text-slate-900">counted_quantity</span>, puis réimporte le fichier.
        </div>
    </div>

    @if ($summary)
        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Dernier inventaire</div>
                <div class="mt-3 text-3xl font-semibold text-slate-900">#{{ $summary['count']->id }}</div>
                <div class="mt-2 text-sm text-slate-500">{{ $summary['count']->counted_at?->format('d/m/Y') ?? 'Date non définie' }}</div>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Manquants</div>
                <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($summary['missing_qty'], 3) }}</div>
                <div class="mt-2 text-sm text-slate-500">Valeur estimée: {{ number_format($summary['missing_value'], 2) }}</div>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Surplus</div>
                <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($summary['surplus_qty'], 3) }}</div>
                <div class="mt-2 text-sm text-slate-500">Valeur estimée: {{ number_format($summary['surplus_value'], 2) }}</div>
            </div>
        </div>
    @endif

    <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        @if ($counts->isEmpty())
            <x-empty-state
                title="Aucun inventaire"
                description="Créez un inventaire pour comparer le stock."
                action="Nouvel inventaire"
                :action-href="route('inventory-counts.create')"
            />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/80">
                        <tr class="text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                            <th class="px-4 py-3">Inventaire</th>
                            <th class="px-4 py-3">Entité</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($counts as $count)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-4">
                                    <div class="font-medium text-slate-900">Inventaire #{{ $count->id }}</div>
                                    <div class="mt-1 text-xs text-slate-500">Comptage enregistré dans l'historique stock.</div>
                                </td>
                                <td class="px-4 py-4 text-sm text-slate-600">{{ $count->location?->name ?? 'Entité supprimée' }}</td>
                                <td class="px-4 py-4 text-sm text-slate-600">{{ $count->counted_at?->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-4 py-4 text-sm text-slate-600">{{ $count->notes ?: 'Aucune note' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    <div>
        {{ $counts->links() }}
    </div>
</div>
