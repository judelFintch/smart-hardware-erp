@php
    $movementTypeLabel = match ($type) {
        'purchase_in' => 'Achat',
        'sale_out' => 'Vente',
        'transfer_in' => 'Transfert entrant',
        'adjustment_in' => 'Ajustement +',
        'adjustment_out' => 'Ajustement -',
        default => 'Tous les types',
    };
@endphp

<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-cyan-50 p-6 shadow-sm">
        <div class="max-w-2xl">
            <div class="inline-flex items-center rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">
                Flux stock
            </div>
            <h1 class="mt-3 text-3xl font-semibold text-slate-900">Historique des mouvements et filtres de traçabilité</h1>
            <p class="mt-2 text-sm text-slate-500">Analyse les entrées, sorties, ajustements et transferts selon l’article, l’entité et la période.</p>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Article</label>
                <select wire:model.live="product_id" class="input mt-2">
                    <option value="">Tous</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Entité</label>
                <select wire:model.live="location_id" class="input mt-2">
                    <option value="">Toutes</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Type</label>
                <select wire:model.live="type" class="input mt-2">
                    <option value="">Tous</option>
                    <option value="purchase_in">Achat</option>
                    <option value="sale_out">Vente</option>
                    <option value="transfer_in">Transfert</option>
                    <option value="adjustment_in">Ajustement +</option>
                    <option value="adjustment_out">Ajustement -</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Du</label>
                <input type="date" wire:model.live="date_from" class="input mt-2">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Au</label>
                <input type="date" wire:model.live="date_to" class="input mt-2">
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Mouvements affichés</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($movements->count()) }}</div>
            <div class="mt-2 text-sm text-slate-500">Résultat filtré sur la page actuelle.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Total résultats</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($movements->total()) }}</div>
            <div class="mt-2 text-sm text-slate-500">Volume complet correspondant aux filtres.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Type actif</div>
            <div class="mt-3 text-2xl font-semibold text-slate-900">{{ $movementTypeLabel }}</div>
            <div class="mt-2 text-sm text-slate-500">Lecture rapide du filtre actuellement appliqué.</div>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        @if ($movements->isEmpty())
            <x-empty-state
                title="Aucun mouvement"
                description="Aucun mouvement ne correspond aux filtres."
            />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/80">
                        <tr class="text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Article</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">De</th>
                            <th class="px-4 py-3">Vers</th>
                            <th class="px-4 py-3 text-right">Quantité</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($movements as $movement)
                            @php
                                $typeClass = match ($movement->type) {
                                    'purchase_in', 'adjustment_in', 'transfer_in', 'return_in' => 'bg-emerald-100 text-emerald-700',
                                    'sale_out', 'adjustment_out' => 'bg-amber-100 text-amber-700',
                                    default => 'bg-slate-100 text-slate-700',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-4 text-sm text-slate-600">{{ $movement->occurred_at?->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-4">
                                    <div class="font-medium text-slate-900">{{ $movement->product?->name ?? 'Article supprimé' }}</div>
                                    <div class="mt-1 text-xs text-slate-500">Mouvement référencé dans le journal stock.</div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $typeClass }}">{{ $movement->type }}</span>
                                </td>
                                <td class="px-4 py-4 text-sm text-slate-600">{{ $movement->fromLocation?->name ?? '—' }}</td>
                                <td class="px-4 py-4 text-sm text-slate-600">{{ $movement->toLocation?->name ?? '—' }}</td>
                                <td class="px-4 py-4 text-right font-semibold text-slate-900">{{ number_format((float) $movement->quantity, 3) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    <div>
        {{ $movements->links() }}
    </div>
</div>
