<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-emerald-50 p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <div class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">
                    Rapport des ventes
                </div>
                <h1 class="mt-3 text-3xl font-semibold text-slate-900">Toutes les lignes de vente avec prix d'achat, prix de vente et bénéfice</h1>
                <p class="mt-2 text-sm text-slate-500">Analyse détaillée par ligne vendue, avec totaux consolidés et export Excel pour la période choisie.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button wire:click="exportExcel" class="btn btn-secondary" type="button">Exporter Excel</button>
                <a href="{{ route('reports.financial') }}" class="btn btn-secondary" wire:navigate>Rapport financier</a>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="applyFilter" class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
        <div class="grid gap-4 md:grid-cols-[1fr_1fr_auto] md:items-end">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Début</label>
                <input wire:model.defer="start" type="date" class="input mt-2">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Fin</label>
                <input wire:model.defer="end" type="date" class="input mt-2">
            </div>
            <button class="btn btn-primary" type="submit">Filtrer</button>
        </div>
    </form>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Lignes vendues</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($totals['lines']) }}</div>
            <div class="mt-2 text-sm text-slate-500">Nombre total de lignes de vente sur le filtre.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Total vente</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($totals['sales'], 2) }}</div>
            <div class="mt-2 text-sm text-slate-500">Somme des prix de vente unitaires multipliés par les quantités.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Total achat</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($totals['purchase'], 2) }}</div>
            <div class="mt-2 text-sm text-slate-500">Somme des coûts unitaires d'achat sur les mêmes lignes.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Bénéfice</div>
            <div class="mt-3 text-3xl font-semibold {{ $totals['profit'] >= 0 ? 'text-emerald-700' : 'text-red-600' }}">{{ number_format($totals['profit'], 2) }}</div>
            <div class="mt-2 text-sm text-slate-500">Différence entre total vente et total achat.</div>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="text-sm font-medium text-slate-900">{{ $saleLines->total() }} ligne(s) trouvée(s)</div>
                <div class="mt-1 text-sm text-slate-500">Quantité cumulée: {{ number_format($totals['quantity'], 3) }}</div>
            </div>
            <div class="text-sm text-slate-500">
                L'export Excel reprend exactement les lignes du filtre affiché.
            </div>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        @if ($saleLines->isEmpty())
            <div class="px-6 py-10 text-sm text-slate-500">Aucune ligne de vente trouvée pour cette période.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/80">
                        <tr class="text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                            <th class="px-4 py-3">Vente</th>
                            <th class="px-4 py-3">Article</th>
                            <th class="px-4 py-3">Client</th>
                            <th class="px-4 py-3">Magasin</th>
                            <th class="px-4 py-3 text-right">Qté</th>
                            <th class="px-4 py-3 text-right">PU vente</th>
                            <th class="px-4 py-3 text-right">PU achat</th>
                            <th class="px-4 py-3 text-right">Total vente</th>
                            <th class="px-4 py-3 text-right">Total achat</th>
                            <th class="px-4 py-3 text-right">Bénéfice</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($saleLines as $line)
                            @php
                                $salesTotal = (float) $line->unit_price * (float) $line->quantity;
                                $purchaseTotal = (float) $line->unit_cost_local * (float) $line->quantity;
                                $profit = $salesTotal - $purchaseTotal;
                            @endphp
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-4">
                                    <div class="font-medium text-slate-900">Vente #{{ $line->sale_id }}</div>
                                    <div class="mt-1 text-xs text-slate-500">{{ $line->sale?->sold_at?->format('d/m/Y H:i') ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="font-medium text-slate-900">{{ $line->product?->name ?? '-' }}</div>
                                    <div class="mt-1 text-xs text-slate-500">{{ $line->product?->sku ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-4 text-slate-700">{{ $line->sale?->customer?->name ?? 'Client comptoir' }}</td>
                                <td class="px-4 py-4 text-slate-700">{{ $line->location?->name ?? '-' }}</td>
                                <td class="px-4 py-4 text-right text-slate-700">{{ number_format((float) $line->quantity, 3) }}</td>
                                <td class="px-4 py-4 text-right text-slate-700">{{ number_format((float) $line->unit_price, 2) }}</td>
                                <td class="px-4 py-4 text-right text-slate-700">{{ number_format((float) $line->unit_cost_local, 2) }}</td>
                                <td class="px-4 py-4 text-right font-medium text-slate-900">{{ number_format($salesTotal, 2) }}</td>
                                <td class="px-4 py-4 text-right font-medium text-slate-900">{{ number_format($purchaseTotal, 2) }}</td>
                                <td class="px-4 py-4 text-right font-semibold {{ $profit >= 0 ? 'text-emerald-700' : 'text-red-600' }}">{{ number_format($profit, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div>
        {{ $saleLines->links() }}
    </div>
</div>
