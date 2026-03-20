<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-emerald-50 p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-3">
                <div class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">
                    Fiche de stock
                </div>
                <div>
                    <h1 class="text-2xl font-semibold text-slate-900">{{ $product->name }}</h1>
                    <p class="mt-1 text-sm text-slate-500">
                        SKU {{ $product->sku }} · {{ $product->unit?->code ?? '—' }} · Code-barres {{ $product->barcode ?: 'non défini' }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-2 text-sm text-slate-600">
                    <span class="rounded-full bg-white px-3 py-1 shadow-sm ring-1 ring-slate-200">Stock actuel: {{ number_format($totalStock, 3) }}</span>
                    <span class="rounded-full bg-white px-3 py-1 shadow-sm ring-1 ring-slate-200">Seuil: {{ number_format((float) $product->reorder_level, 3) }}</span>
                    <span class="rounded-full bg-white px-3 py-1 shadow-sm ring-1 ring-slate-200">Marge: {{ number_format((float) $product->sale_margin_percent, 2) }}%</span>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('products.index') }}" class="btn btn-secondary" wire:navigate>Retour articles</a>
                <a href="{{ route('products.edit', $product) }}" class="btn btn-primary" wire:navigate>Modifier</a>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Quantité totale</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($totalStock, 3) }}</div>
            <div class="mt-2 text-sm text-slate-500">Disponible sur l'ensemble des emplacements.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Valeur du stock</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($stockValue, 2) }}</div>
            <div class="mt-2 text-sm text-slate-500">Quantité en stock multipliée par le coût moyen par emplacement.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Emplacements actifs</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ $activeLocations }}</div>
            <div class="mt-2 text-sm text-slate-500">Dépôts ou magasins avec un solde positif.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Dernier mouvement</div>
            <div class="mt-3 text-lg font-semibold text-slate-900">{{ $lastMovementAt?->format('d/m/Y H:i') ?? 'Aucun' }}</div>
            <div class="mt-2 text-sm text-slate-500">Dernière activité enregistrée pour cet article.</div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[0.95fr_1.55fr]">
        <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Stock par emplacement</h2>
                <p class="mt-1 text-sm text-slate-500">Vue instantanée des soldes disponibles.</p>
            </div>
            @if ($balances->isEmpty())
                <div class="px-6 py-10 text-sm text-slate-500">Aucun solde disponible pour cet article.</div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach ($balances as $balance)
                        <div class="flex items-center justify-between gap-4 px-6 py-4">
                            <div>
                                <div class="font-medium text-slate-900">{{ $balance->location?->name ?? 'Sans emplacement' }}</div>
                                <div class="mt-1 text-xs text-slate-500">Coût moyen {{ number_format((float) $balance->avg_cost_local, 2) }} · Prix vente {{ number_format((float) $balance->sale_price_local, 2) }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-semibold text-slate-900">{{ number_format((float) $balance->quantity, 3) }}</div>
                                <div class="mt-1 text-xs text-slate-500">unités</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Mouvements de stock</h2>
                <p class="mt-1 text-sm text-slate-500">Journal chronologique avec variation et solde global après mouvement.</p>
            </div>
            @if ($movements->isEmpty())
                <div class="px-6 py-10 text-sm text-slate-500">Aucun mouvement de stock enregistré pour cet article.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-slate-50/80">
                            <tr class="text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3">Type</th>
                                <th class="px-6 py-3">Référence</th>
                                <th class="px-6 py-3">De / Vers</th>
                                <th class="px-6 py-3 text-right">Variation</th>
                                <th class="px-6 py-3 text-right">Solde après</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($movements as $movement)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-6 py-4 text-sm text-slate-500">{{ $movement->occurred_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                                            {{ $this->formatMovementType($movement->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600">{{ $this->formatReference($movement->reference_type, $movement->reference_id) }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-600">
                                        {{ $movement->fromLocation?->name ?? '—' }} → {{ $movement->toLocation?->name ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-semibold {{ $movement->stock_delta > 0 ? 'text-emerald-600' : ($movement->stock_delta < 0 ? 'text-rose-600' : 'text-slate-500') }}">
                                        {{ $movement->stock_delta > 0 ? '+' : '' }}{{ number_format((float) $movement->stock_delta, 3) }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-semibold text-slate-900">{{ number_format((float) $movement->stock_after, 3) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-100 px-6 py-4">
                    {{ $movements->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
