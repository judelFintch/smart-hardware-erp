@php
    $itemsCount = $sale->items->count();
    $totalQuantity = (float) $sale->items->sum('quantity');
    $remaining = max(0, (float) $sale->total_amount - (float) $sale->paid_total);
    $statusClass = $sale->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700';
    $typeClass = $sale->type === 'cash' ? 'bg-cyan-100 text-cyan-700' : 'bg-violet-100 text-violet-700';
@endphp

<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-emerald-50 p-6 shadow-sm">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
            <div class="space-y-4">
                <div class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">
                    Détail vente
                </div>
                <div>
                    <h1 class="text-3xl font-semibold text-slate-900">Vente #{{ $sale->id }}</h1>
                    <p class="mt-2 text-sm text-slate-500">
                        {{ $sale->customer?->name ?? 'Client comptoir' }} ·
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $typeClass }}">{{ $sale->type === 'cash' ? 'Comptant' : 'Crédit' }}</span>
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClass }}">{{ $sale->status === 'paid' ? 'Soldée' : 'Ouverte' }}</span>
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <a class="btn btn-secondary" href="{{ route('sales.index') }}" wire:navigate>Retour</a>
                <a class="btn btn-primary" href="{{ route('sales.print', $sale) }}" target="_blank" rel="noopener">Imprimer facture</a>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Articles</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($itemsCount) }}</div>
            <div class="mt-2 text-sm text-slate-500">Lignes enregistrées dans la vente.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Quantité nette</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($totalQuantity, 3) }}</div>
            <div class="mt-2 text-sm text-slate-500">Prend aussi en compte les retours déjà saisis.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Sous-total</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format((float) $sale->subtotal, 2) }}</div>
            <div class="mt-2 text-sm text-slate-500">Avant application de la remise globale.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Total payé</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format((float) $sale->paid_total, 2) }}</div>
            <div class="mt-2 text-sm text-slate-500">Montant déjà encaissé sur la vente.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Reste dû</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($remaining, 2) }}</div>
            <div class="mt-2 text-sm text-slate-500">Solde encore attendu si la vente est à crédit.</div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.45fr_0.95fr]">
        <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Articles vendus</h2>
                <p class="mt-1 text-sm text-slate-500">Vue détaillée des quantités, prix unitaires et totaux de ligne.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/80">
                        <tr class="text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                            <th class="px-4 py-3">Article</th>
                            <th class="px-4 py-3 text-right">Quantité</th>
                            <th class="px-4 py-3 text-right">Prix</th>
                            <th class="px-4 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($sale->items as $item)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-4">
                                    <div class="font-medium text-slate-900">{{ $item->product->name }}</div>
                                    <div class="mt-1 text-xs text-slate-500">{{ $item->quantity < 0 ? 'Retour enregistré sur cette ligne.' : 'Ligne de vente standard.' }}</div>
                                </td>
                                <td class="px-4 py-4 text-right font-medium text-slate-700">{{ number_format((float) $item->quantity, 3) }}</td>
                                <td class="px-4 py-4 text-right font-medium text-slate-700">{{ number_format((float) $item->unit_price, 2) }}</td>
                                <td class="px-4 py-4 text-right font-semibold text-slate-900">{{ number_format((float) $item->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Résumé financier</h2>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Sous-total</span>
                        <span class="font-semibold text-slate-900">{{ number_format((float) $sale->subtotal, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Remise globale</span>
                        <span class="font-semibold text-slate-900">{{ number_format((float) $sale->discount_total, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Total net</span>
                        <span class="font-semibold text-slate-900">{{ number_format((float) $sale->total_amount, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Payé</span>
                        <span class="font-semibold text-slate-900">{{ number_format((float) $sale->paid_total, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                        <span class="text-slate-500">Reste dû</span>
                        <span class="text-lg font-semibold text-slate-900">{{ number_format($remaining, 2) }}</span>
                    </div>
                </div>
            </div>

            @if ((float) $sale->discount_total > 0)
                <div class="rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-800 shadow-sm">
                    Remise globale appliquée sur cette vente: <strong>{{ number_format((float) $sale->discount_total, 2) }}</strong>
                </div>
            @endif

            <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Retour / Échange</h2>
                <p class="mt-1 text-sm text-slate-500">Réintègre la marchandise retournée et trace le mouvement de stock entrant.</p>
                <form wire:submit.prevent="returnItem" class="mt-4 space-y-3">
                    <select wire:model.defer="return_product_id" class="input" required>
                        @foreach ($sale->items->where('quantity', '>', 0) as $item)
                            <option value="{{ $item->product_id }}">{{ $item->product->name }}</option>
                        @endforeach
                    </select>
                    <input wire:model.defer="return_quantity" type="number" step="0.001" class="input" placeholder="Quantité retournée" required>
                    <div class="flex justify-end">
                        <button class="btn btn-secondary text-amber-700" type="submit">Enregistrer retour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($sale->type === 'credit')
        <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Paiements</h2>
                <p class="mt-1 text-sm text-slate-500">Ajoute les encaissements et garde la trace du règlement de la vente.</p>
            </div>

            <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr] p-6">
                <form wire:submit.prevent="addPayment" class="space-y-3 rounded-3xl border border-slate-200 bg-slate-50/70 p-5">
                    <div class="grid gap-3 md:grid-cols-2">
                        <input wire:model.defer="payment_amount" type="number" step="0.01" class="input" placeholder="Montant" required>
                        <input wire:model.defer="payment_paid_at" type="date" class="input">
                    </div>
                    <div class="grid gap-3 md:grid-cols-2">
                        <input wire:model.defer="payment_method" class="input" placeholder="Méthode">
                        <input wire:model.defer="payment_reference" class="input" placeholder="Référence">
                    </div>
                    <textarea wire:model.defer="payment_notes" class="input" placeholder="Notes"></textarea>
                    <div class="flex justify-end">
                        <button class="btn btn-primary" type="submit">Ajouter paiement</button>
                    </div>
                </form>

                <div class="rounded-3xl border border-slate-200 bg-white overflow-hidden">
                    <table class="min-w-full">
                        <thead class="bg-slate-50/80">
                            <tr class="text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3 text-right">Montant</th>
                                <th class="px-4 py-3">Méthode</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($sale->payments as $payment)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-4 py-4 text-sm text-slate-600">{{ $payment->paid_at ?: '—' }}</td>
                                    <td class="px-4 py-4 text-right font-semibold text-slate-900">{{ number_format((float) $payment->amount, 2) }}</td>
                                    <td class="px-4 py-4 text-sm text-slate-600">{{ $payment->method ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-sm text-slate-500">Aucun paiement enregistré pour cette vente.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
