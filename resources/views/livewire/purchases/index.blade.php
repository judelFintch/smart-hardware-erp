@php
    $currentPageTotal = (float) $purchases->getCollection()->sum('total_cost_local');
@endphp

<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-cyan-50 p-6 shadow-sm">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-2xl">
                <div class="inline-flex items-center rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">
                    Gestion des achats
                </div>
                <h1 class="mt-3 text-3xl font-semibold text-slate-900">Suivi fournisseurs, réceptions et documents d'achat</h1>
                <p class="mt-2 text-sm text-slate-500">Consulte les commandes, repère les achats encore en attente et ouvre rapidement le détail ou la modification.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button wire:click="export" class="btn btn-secondary" type="button">Exporter Excel</button>
                <button wire:click="exportPdf" class="btn btn-secondary" type="button">Exporter PDF</button>
                <a href="{{ route('purchases.create') }}" class="btn btn-primary" wire:navigate>Nouvel achat</a>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Achats</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($stats['count']) }}</div>
            <div class="mt-2 text-sm text-slate-500">Nombre total de bons d'achat enregistrés.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Montant cumulé</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($stats['total_cost'], 2) }}</div>
            <div class="mt-2 text-sm text-slate-500">Coût total local de l'ensemble des achats.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">En cours</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($stats['in_progress']) }}</div>
            <div class="mt-2 text-sm text-slate-500">Achats non encore approvisionnés.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Approvisionnés</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($stats['received']) }}</div>
            <div class="mt-2 text-sm text-slate-500">Achats déjà réceptionnés et stockés.</div>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white p-4 shadow-sm">
        <div class="grid gap-4 xl:grid-cols-[1fr_auto] xl:items-end">
            <div>
                <div class="text-sm font-medium text-slate-900">{{ $purchases->total() }} achat(s) trouvé(s)</div>
                <div class="mt-1 text-sm text-slate-500">Page en cours: {{ number_format($currentPageTotal, 2) }} au total.</div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <form wire:submit.prevent="importCsv" class="flex flex-wrap items-center gap-2">
                    <input type="file" wire:model="importFile" class="input bg-white" />
                    <button class="btn btn-secondary" type="submit">Importer CSV</button>
                </form>
                @error('importFile') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        @if ($purchases->isEmpty())
            <x-empty-state
                title="Aucun achat"
                description="Créez votre premier achat fournisseur."
                action="Nouvel achat"
                :action-href="route('purchases.create')"
            />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/80">
                        <tr class="text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                            <th class="px-4 py-3">Achat</th>
                            <th class="px-4 py-3">Fournisseur</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Statut</th>
                            <th class="px-4 py-3">Destination</th>
                            <th class="px-4 py-3 text-right">Montant</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($purchases as $purchase)
                            @php
                                $statusLabel = match ($purchase->status) {
                                    'commande' => 'Commande',
                                    'en_cours' => 'En cours',
                                    'en_fabrication' => 'En fabrication',
                                    'livree_agence' => 'Livrée agence',
                                    'en_transit' => 'En transit',
                                    'receptionnee' => 'Réceptionnée',
                                    'approvisionnee' => 'Approvisionnée',
                                    default => ucfirst((string) str_replace('_', ' ', $purchase->status)),
                                };
                                $statusClass = match ($purchase->status) {
                                    'approvisionnee' => 'bg-emerald-100 text-emerald-700',
                                    'receptionnee' => 'bg-cyan-100 text-cyan-700',
                                    'en_transit', 'livree_agence' => 'bg-amber-100 text-amber-700',
                                    default => 'bg-slate-100 text-slate-700',
                                };
                                $typeClass = $purchase->type === 'foreign' ? 'bg-violet-100 text-violet-700' : 'bg-cyan-100 text-cyan-700';
                            @endphp
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-4">
                                    <div class="font-medium text-slate-900">Achat #{{ $purchase->id }}</div>
                                    <div class="mt-1 text-xs text-slate-500">
                                        Réf: {{ $purchase->reference ?: 'Non définie' }} · {{ $purchase->ordered_at?->format('d/m/Y') ?? 'Date non définie' }}
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="font-medium text-slate-900">{{ $purchase->supplier->name }}</div>
                                    <div class="mt-1 text-xs text-slate-500">{{ $purchase->currency ?: 'CDF' }} · Taux {{ number_format((float) $purchase->exchange_rate, 2) }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $typeClass }}">
                                        {{ $purchase->type === 'foreign' ? 'Import / Étranger' : 'Local' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm text-slate-600">
                                    {{ $purchase->receiveLocation?->name ?? 'À définir' }}
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <div class="font-semibold text-slate-900">{{ number_format((float) $purchase->total_cost_local, 2) }}</div>
                                    <div class="mt-1 text-xs text-slate-500">Sous-total: {{ number_format((float) $purchase->subtotal_local, 2) }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-secondary" wire:navigate>Voir</a>
                                        <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-secondary" wire:navigate>Modifier</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    <div>
        {{ $purchases->links() }}
    </div>
</div>
