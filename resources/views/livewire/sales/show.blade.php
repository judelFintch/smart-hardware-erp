@php
    $itemsCount = $sale->items->count();
    $totalQuantity = (float) $sale->items->sum('quantity');
    $remaining = max(0, (float) $sale->total_amount - (float) $sale->paid_total);
    $purchaseTotal = (float) $sale->items->sum(fn ($item) => (float) $item->unit_cost_local * (float) $item->quantity);
    $profitTotal = (float) $sale->items->sum(fn ($item) => ((float) $item->unit_price - (float) $item->unit_cost_local) * (float) $item->quantity);
    $statusClass = $sale->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700';
    $typeClass = $sale->type === 'cash' ? 'bg-cyan-100 text-cyan-700' : 'bg-violet-100 text-violet-700';
@endphp

<div class="space-y-6">
    <div class="rounded-[26px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-emerald-50 p-5 shadow-sm">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
            <div class="space-y-3">
                <div class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-emerald-700">
                    Détail vente
                </div>
                <div>
                    <h1 class="text-2xl font-semibold text-slate-900">Vente #{{ $sale->id }}</h1>
                    <p class="mt-1 text-sm text-slate-500">
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

    <div class="rounded-[24px] border border-slate-200 bg-white px-3 py-2.5 shadow-sm">
        <div class="grid gap-x-4 gap-y-1.5 md:grid-cols-3 xl:grid-cols-7">
            <div class="flex min-w-0 items-baseline justify-between gap-2 border-b border-slate-100 py-1.5 xl:border-b-0 xl:border-r xl:pr-3">
                <span class="truncate text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Articles</span>
                <span class="shrink-0 text-sm font-semibold text-slate-900">{{ number_format($itemsCount) }}</span>
            </div>
            <div class="flex min-w-0 items-baseline justify-between gap-2 border-b border-slate-100 py-1.5 xl:border-b-0 xl:border-r xl:px-3">
                <span class="truncate text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Qté</span>
                <span class="shrink-0 text-sm font-semibold text-slate-900">{{ number_format($totalQuantity, 3) }}</span>
            </div>
            <div class="flex min-w-0 items-baseline justify-between gap-2 border-b border-slate-100 py-1.5 xl:border-b-0 xl:border-r xl:px-3">
                <span class="truncate text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Sous-total</span>
                <span class="shrink-0 text-[13px] font-semibold text-slate-900">{{ number_format((float) $sale->subtotal, 2) }}</span>
            </div>
            <div class="flex min-w-0 items-baseline justify-between gap-2 border-b border-slate-100 py-1.5 xl:border-b-0 xl:border-r xl:px-3">
                <span class="truncate text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Achat</span>
                <span class="shrink-0 text-[13px] font-semibold text-slate-900">{{ number_format($purchaseTotal, 2) }}</span>
            </div>
            <div class="flex min-w-0 items-baseline justify-between gap-2 border-b border-slate-100 py-1.5 xl:border-b-0 xl:border-r xl:px-3">
                <span class="truncate text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Payé</span>
                <span class="shrink-0 text-[13px] font-semibold text-slate-900">{{ number_format((float) $sale->paid_total, 2) }}</span>
            </div>
            <div class="flex min-w-0 items-baseline justify-between gap-2 border-b border-slate-100 py-1.5 xl:border-b-0 xl:border-r xl:px-3">
                <span class="truncate text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Reste</span>
                <span class="shrink-0 text-[13px] font-semibold text-slate-900">{{ number_format($remaining, 2) }}</span>
            </div>
            <div class="flex min-w-0 items-baseline justify-between gap-2 py-1.5 xl:pl-3">
                <span class="truncate text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Bénéfice</span>
                <span class="shrink-0 text-[13px] font-semibold {{ $profitTotal >= 0 ? 'text-emerald-700' : 'text-red-600' }}">{{ number_format($profitTotal, 2) }}</span>
            </div>
        </div>
    </div>

    <div class="grid gap-5 xl:grid-cols-[minmax(0,1.7fr)_340px]">
        <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 px-5 py-3.5">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Articles vendus</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Prix de vente, achat et bénéfice par ligne.</p>
                    </div>
                    <div class="hidden text-xs text-slate-500 md:block">
                        {{ $itemsCount }} ligne(s)
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/80">
                        <tr class="text-left text-[11px] uppercase tracking-[0.14em] text-slate-500">
                            <th class="px-5 py-3">Article</th>
                            <th class="px-3 py-3 text-right">Qté</th>
                            <th class="px-3 py-3 text-right">PV</th>
                            <th class="px-3 py-3 text-right">PA</th>
                            <th class="px-3 py-3 text-right">Vente</th>
                            <th class="px-3 py-3 text-right">Achat</th>
                            <th class="px-5 py-3 text-right">Bénéfice</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($sale->items as $item)
                            @php
                                $linePurchaseTotal = (float) $item->unit_cost_local * (float) $item->quantity;
                                $lineProfit = ((float) $item->unit_price - (float) $item->unit_cost_local) * (float) $item->quantity;
                            @endphp
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-5 py-4">
                                    <div class="font-medium leading-5 text-slate-900">{{ $item->product->name }}</div>
                                    <div class="mt-1 text-[11px] text-slate-500">{{ $item->quantity < 0 ? 'Retour' : 'Vente' }}</div>
                                </td>
                                <td class="px-3 py-4 text-right text-sm font-medium text-slate-700">{{ number_format((float) $item->quantity, 3) }}</td>
                                <td class="px-3 py-4 text-right text-sm font-semibold text-slate-900">{{ number_format((float) $item->unit_price, 2) }}</td>
                                <td class="px-3 py-4 text-right text-sm font-semibold text-slate-900">{{ number_format((float) $item->unit_cost_local, 2) }}</td>
                                <td class="px-3 py-4 text-right text-sm font-semibold text-slate-900">{{ number_format((float) $item->line_total, 2) }}</td>
                                <td class="px-3 py-4 text-right text-sm font-semibold text-slate-900">{{ number_format($linePurchaseTotal, 2) }}</td>
                                <td class="px-5 py-4 text-right text-base font-semibold {{ $lineProfit >= 0 ? 'text-emerald-700' : 'text-red-600' }}">{{ number_format($lineProfit, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50/70">
                        <tr class="text-sm text-slate-700">
                            <td class="px-5 py-3 font-semibold text-slate-900">Totaux</td>
                            <td class="px-3 py-3 text-right font-semibold text-slate-900">{{ number_format($totalQuantity, 3) }}</td>
                            <td class="px-3 py-3"></td>
                            <td class="px-3 py-3"></td>
                            <td class="px-3 py-3 text-right font-semibold text-slate-900">{{ number_format((float) $sale->subtotal, 2) }}</td>
                            <td class="px-3 py-3 text-right font-semibold text-slate-900">{{ number_format($purchaseTotal, 2) }}</td>
                            <td class="px-5 py-3 text-right font-semibold {{ $profitTotal >= 0 ? 'text-emerald-700' : 'text-red-600' }}">{{ number_format($profitTotal, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-base font-semibold text-slate-900">Résumé financier</h2>
                <div class="mt-4 grid gap-2">
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2 text-sm">
                        <span class="text-slate-500">Sous-total</span>
                        <span class="font-semibold text-slate-900">{{ number_format((float) $sale->subtotal, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2 text-sm">
                        <span class="text-slate-500">Remise</span>
                        <span class="font-semibold text-slate-900">{{ number_format((float) $sale->discount_total, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2 text-sm">
                        <span class="text-slate-500">Total net</span>
                        <span class="font-semibold text-slate-900">{{ number_format((float) $sale->total_amount, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2 text-sm">
                        <span class="text-slate-500">Total achat</span>
                        <span class="font-semibold text-slate-900">{{ number_format($purchaseTotal, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl bg-emerald-50 px-3 py-2 text-sm">
                        <span class="text-emerald-700">Bénéfice</span>
                        <span class="font-semibold {{ $profitTotal >= 0 ? 'text-emerald-700' : 'text-red-600' }}">{{ number_format($profitTotal, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2 text-sm">
                        <span class="text-slate-500">Payé</span>
                        <span class="font-semibold text-slate-900">{{ number_format((float) $sale->paid_total, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl bg-slate-900 px-3 py-2 text-sm">
                        <span class="text-slate-300">Reste dû</span>
                        <span class="text-base font-semibold text-white">{{ number_format($remaining, 2) }}</span>
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
                <p class="mt-1 text-sm text-slate-500">Le retour rembourse la valeur de l’article via une ligne négative. L’échange conserve le montant encaissé et remplace l’article par un autre, avec traçabilité de l’état retourné.</p>
                @if ($returnableItems->isEmpty())
                    <div class="mt-4 rounded-2xl border border-dashed border-slate-300 px-4 py-6 text-sm text-slate-500">
                        Tous les articles de cette vente ont déjà été totalement retournés ou échangés.
                    </div>
                @else
                <form wire:submit.prevent="processAdjustment" class="mt-4 space-y-3">
                    <div class="grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-slate-400">Opération</label>
                            <select wire:model.live="adjustment_type" class="input" required>
                                <option value="return">Retour</option>
                                <option value="exchange">Échange</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-slate-400">État du retour</label>
                            <select wire:model.defer="return_condition" class="input" required>
                                <option value="good">Bon état</option>
                                <option value="damaged">Endommagé</option>
                                <option value="broken">Foutu</option>
                                <option value="defective">Défectueux</option>
                                <option value="other">Autre</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-slate-400">Article retourné</label>
                        <select wire:model.live="return_product_id" class="input" required>
                            @foreach ($returnableItems as $item)
                                <option value="{{ $item['product_id'] }}">{{ $item['name'] }} · Restant: {{ number_format((float) $item['quantity'], 3) }}</option>
                            @endforeach
                        </select>
                        @error('return_product_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-slate-400">Quantité retournée</label>
                        <input wire:model.defer="return_quantity" type="number" step="0.001" class="input" placeholder="Quantité retournée" required>
                        @error('return_quantity') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    @if ($adjustment_type === 'exchange')
                        <div class="rounded-2xl border border-cyan-200 bg-cyan-50/60 p-4">
                            <div class="text-sm font-semibold text-cyan-900">Article de remplacement</div>
                            <div class="mt-1 text-xs text-cyan-800">Le montant déjà encaissé est conservé. La valeur de l’article retourné est affectée au nouvel article.</div>
                            <div class="mt-3 grid gap-3">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-slate-400">Nouvel article</label>
                                    <select wire:model.defer="exchange_product_id" class="input" required>
                                        <option value="">-- Choisir --</option>
                                        @foreach ($exchangeableProducts as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }} · Stock: {{ number_format((float) ($exchangeStocks[$product->id] ?? 0), 3) }}</option>
                        @endforeach
                                    </select>
                                    @error('exchange_product_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-slate-400">Quantité échangée</label>
                                    <input wire:model.defer="exchange_quantity" type="number" step="0.001" class="input" placeholder="Quantité de remplacement" required>
                                    @error('exchange_quantity') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-slate-400">Notes</label>
                        <textarea wire:model.defer="return_notes" class="input" rows="3" placeholder="Ex: emballage abîmé, écran cassé, accessoire manquant..."></textarea>
                        @error('return_notes') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-end">
                        <button class="btn btn-secondary text-amber-700" type="submit">{{ $adjustment_type === 'exchange' ? 'Enregistrer échange' : 'Enregistrer retour' }}</button>
                    </div>
                </form>
                @endif
            </div>

            <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Historique retours / échanges</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($sale->adjustments as $adjustment)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $adjustment->type === 'exchange' ? 'bg-cyan-100 text-cyan-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $adjustment->type === 'exchange' ? 'Échange' : 'Retour' }}
                                    </span>
                                    <span class="text-xs text-slate-500">{{ $adjustment->processed_at?->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="text-sm font-semibold text-slate-900">{{ number_format((float) $adjustment->amount_local, 2) }}</div>
                            </div>
                            <div class="mt-3 text-sm text-slate-700">
                                <div><span class="font-medium text-slate-900">Retourné:</span> {{ $adjustment->originalProduct?->name ?? 'Article supprimé' }} · Qté {{ number_format((float) $adjustment->original_quantity, 3) }}</div>
                                @if ($adjustment->type === 'exchange')
                                    <div class="mt-1"><span class="font-medium text-slate-900">Remplacé par:</span> {{ $adjustment->replacementProduct?->name ?? 'Article supprimé' }} · Qté {{ number_format((float) ($adjustment->replacement_quantity ?? 0), 3) }}</div>
                                @endif
                                <div class="mt-1"><span class="font-medium text-slate-900">État:</span> {{ $this->conditionLabel($adjustment->item_condition) }}</div>
                                <div class="mt-1"><span class="font-medium text-slate-900">Entité:</span> {{ $adjustment->location?->name ?? '—' }}</div>
                                @if (filled($adjustment->notes))
                                    <div class="mt-1"><span class="font-medium text-slate-900">Notes:</span> {{ $adjustment->notes }}</div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-6 text-sm text-slate-500">
                            Aucun retour ou échange enregistré pour cette vente.
                        </div>
                    @endforelse
                </div>
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
