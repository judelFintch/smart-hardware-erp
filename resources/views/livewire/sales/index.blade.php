@php
    $currentPageRevenue = (float) $sales->getCollection()->sum('total_amount');
@endphp

<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-emerald-50 p-6 shadow-sm">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-2xl">
                <div class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">
                    Gestion des ventes
                </div>
                <h1 class="mt-3 text-3xl font-semibold text-slate-900">Suivi des ventes, encaissements et impression rapide</h1>
                <p class="mt-2 text-sm text-slate-500">Consultez les opérations récentes, ouvrez le détail d'une vente et lancez l'impression thermique sans quitter la liste.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button wire:click="export" class="btn btn-secondary" type="button">Exporter Excel</button>
                <button wire:click="exportPdf" class="btn btn-secondary" type="button">Exporter PDF</button>
                @if (in_array(auth()->user()->role, ['owner', 'manager'], true))
                    <a href="{{ route('reports.sales') }}" class="btn btn-secondary" wire:navigate>Rapport détaillé</a>
                @endif
                <a href="{{ route('sales.create') }}" class="btn btn-primary" wire:navigate>Nouvelle vente</a>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Ventes</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($stats['count']) }}</div>
            <div class="mt-2 text-sm text-slate-500">Nombre total d'opérations enregistrées.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Chiffre d'affaires</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($stats['revenue'], 2) }}</div>
            <div class="mt-2 text-sm text-slate-500">Montant cumulé des ventes validées.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Ventes soldées</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($stats['paid']) }}</div>
            <div class="mt-2 text-sm text-slate-500">Règlement complet déjà enregistré.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Ventes ouvertes</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($stats['open']) }}</div>
            <div class="mt-2 text-sm text-slate-500">Crédits ou paiements encore incomplets.</div>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="text-sm font-medium text-slate-900">{{ $sales->total() }} vente(s) trouvée(s)</div>
                <div class="mt-1 text-sm text-slate-500">Page en cours: {{ number_format($currentPageRevenue, 2) }} au total.</div>
            </div>
            <div class="text-sm text-slate-500">
                Les encaissements et impressions sont accessibles directement depuis chaque ligne.
            </div>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        @if ($sales->isEmpty())
            <x-empty-state
                title="Aucune vente"
                description="Enregistrez votre première vente."
                action="Nouvelle vente"
                :action-href="route('sales.create')"
            />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/80">
                        <tr class="text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                            <th class="px-4 py-3">Vente</th>
                            <th class="px-4 py-3">Client</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Statut</th>
                            <th class="px-4 py-3 text-right">Montants</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($sales as $sale)
                            @php
                                $statusClass = $sale->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700';
                                $typeClass = $sale->type === 'cash' ? 'bg-cyan-100 text-cyan-700' : 'bg-violet-100 text-violet-700';
                                $remaining = max(0, (float) $sale->total_amount - (float) $sale->paid_total);
                            @endphp
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-4">
                                    <div class="font-medium text-slate-900">Vente #{{ $sale->id }}</div>
                                    <div class="mt-1 text-xs text-slate-500">{{ $sale->sold_at?->format('d/m/Y H:i') ?? 'Date non définie' }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="font-medium text-slate-900">{{ $sale->customer?->name ?? 'Client comptoir' }}</div>
                                    <div class="mt-1 text-xs text-slate-500">
                                        {{ $sale->type === 'credit' ? 'Facturation avec suivi de paiement.' : 'Encaissement direct au comptant.' }}
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $typeClass }}">
                                        {{ $sale->type === 'cash' ? 'Comptant' : 'Crédit' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClass }}">
                                        {{ $sale->status === 'paid' ? 'Soldée' : 'Ouverte' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <div class="font-semibold text-slate-900">{{ number_format((float) $sale->total_amount, 2) }}</div>
                                    <div class="mt-1 text-xs text-slate-500">
                                        Payé: {{ number_format((float) $sale->paid_total, 2) }} · Reste: {{ number_format($remaining, 2) }}
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <a href="{{ route('sales.show', $sale) }}" class="btn btn-secondary" wire:navigate>Voir</a>
                                        <a href="{{ route('sales.print', $sale) }}" class="btn btn-secondary text-emerald-700" target="_blank" rel="noopener">Impression thermique</a>
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
        {{ $sales->links() }}
    </div>
</div>
