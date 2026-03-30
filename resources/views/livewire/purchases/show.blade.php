@php
    $orderedQty = (float) $purchaseOrder->items->sum('quantity');
    $receivedQty = (float) $purchaseOrder->items->sum(fn ($item) => (float) ($item->received_quantity ?? 0));
    $pendingQty = max(0, $orderedQty - $receivedQty);
    $progress = $orderedQty > 0 ? min(100, ($receivedQty / $orderedQty) * 100) : 0;
    $transferredForeign = (float) $purchaseOrder->transfers->sum('amount_foreign');
    $transferredLocal = (float) $purchaseOrder->transfers->sum('amount_local');
    $attachmentsCount = $purchaseOrder->attachments->count();

    $statusLabel = match ($purchaseOrder->status) {
        'commande' => 'Commande',
        'en_cours' => 'En cours',
        'en_fabrication' => 'En fabrication',
        'livree_agence' => 'Livrée agence',
        'en_transit' => 'En transit',
        'receptionnee' => 'Réceptionnée',
        'approvisionnee' => 'Approvisionnée',
        default => ucfirst((string) str_replace('_', ' ', $purchaseOrder->status)),
    };

    $typeLabel = $purchaseOrder->type === 'foreign' ? 'Import / Étranger' : 'Local';
    $statusBadge = match ($purchaseOrder->status) {
        'approvisionnee' => 'bg-emerald-100 text-emerald-700',
        'receptionnee' => 'bg-cyan-100 text-cyan-700',
        'en_transit', 'livree_agence' => 'bg-amber-100 text-amber-700',
        default => 'bg-slate-100 text-slate-700',
    };
    $currencyLabel = $purchaseOrder->currency ?: '—';
@endphp

<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-cyan-50 p-6 shadow-sm">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
            <div class="space-y-4">
                <div class="inline-flex items-center rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">
                    Détail achat
                </div>
                <div>
                    <h1 class="text-3xl font-semibold text-slate-900">Bon d'achat #{{ $purchaseOrder->id }}</h1>
                    <p class="mt-2 text-sm text-slate-500">
                        {{ $purchaseOrder->supplier->name }} · {{ $typeLabel }} ·
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusBadge }}">{{ $statusLabel }}</span>
                    </p>
                </div>
                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
                    <div class="rounded-2xl bg-white/90 px-4 py-3 shadow-sm ring-1 ring-slate-200">
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Référence</div>
                        <div class="mt-2 text-base font-semibold text-slate-900">{{ $purchaseOrder->reference ?: 'Non définie' }}</div>
                    </div>
                    <div class="rounded-2xl bg-white/90 px-4 py-3 shadow-sm ring-1 ring-slate-200">
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Total local</div>
                        <div class="mt-2 text-base font-semibold text-slate-900">{{ number_format((float) $purchaseOrder->total_cost_local, 2) }}</div>
                    </div>
                    <div class="rounded-2xl bg-white/90 px-4 py-3 shadow-sm ring-1 ring-slate-200">
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Commandé le</div>
                        <div class="mt-2 text-base font-semibold text-slate-900">{{ $purchaseOrder->ordered_at?->format('d/m/Y') ?? '—' }}</div>
                    </div>
                    <div class="rounded-2xl bg-white/90 px-4 py-3 shadow-sm ring-1 ring-slate-200">
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Destination stock</div>
                        <div class="mt-2 text-base font-semibold text-slate-900">{{ $purchaseOrder->receiveLocation?->name ?? 'À définir' }}</div>
                    </div>
                    <div class="rounded-2xl bg-white/90 px-4 py-3 shadow-sm ring-1 ring-slate-200">
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Réception</div>
                        <div class="mt-2 text-base font-semibold text-slate-900">{{ $purchaseOrder->received_at?->format('d/m/Y') ?? 'En attente' }}</div>
                    </div>
                    <div class="rounded-2xl bg-white/90 px-4 py-3 shadow-sm ring-1 ring-slate-200">
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Devise</div>
                        <div class="mt-2 text-base font-semibold text-slate-900">{{ $currencyLabel }}</div>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 xl:max-w-sm xl:justify-end">
                <a href="{{ route('purchases.index') }}" class="btn btn-secondary" wire:navigate>Retour</a>
                <a href="{{ route('purchases.edit', $purchaseOrder) }}" class="btn btn-secondary" wire:navigate>Modifier</a>
                <a href="{{ route('purchases.print', $purchaseOrder) }}" class="btn btn-primary" target="_blank">Imprimer PDF</a>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Articles</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ $purchaseOrder->items->count() }}</div>
            <div class="mt-2 text-sm text-slate-500">Nombre de lignes dans la commande.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Quantité commandée</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($orderedQty, 3) }}</div>
            <div class="mt-2 text-sm text-slate-500">Total des quantités prévues.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Quantité réceptionnée</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($receivedQty, 3) }}</div>
            <div class="mt-2 text-sm text-slate-500">Déjà comptabilisée sur la commande.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Reste à réceptionner</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($pendingQty, 3) }}</div>
            <div class="mt-2 text-sm text-slate-500">Quantité encore attendue sur cet achat.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Avancement réception</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($progress, 1) }}%</div>
            <div class="mt-3 h-2 rounded-full bg-slate-100">
                <div class="h-2 rounded-full bg-cyan-500" style="width: {{ $progress }}%"></div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.55fr_0.95fr]">
        <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 px-6 py-4">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Lignes d'achat</h2>
                        <p class="mt-1 text-sm text-slate-500">Quantités commandées, réceptionnées et coût unitaire par article.</p>
                    </div>
                    <div class="flex flex-wrap gap-2 text-xs font-medium text-slate-600">
                        <span class="rounded-full bg-slate-100 px-3 py-1">Sous-total local: {{ number_format((float) $purchaseOrder->subtotal_local, 2) }}</span>
                        <span class="rounded-full bg-slate-100 px-3 py-1">Total coût: {{ number_format((float) $purchaseOrder->total_cost_local, 2) }}</span>
                    </div>
                </div>
            </div>

            @if ($purchaseOrder->items->isEmpty())
                <div class="px-6 py-10 text-sm text-slate-500">Aucune ligne d'achat enregistrée.</div>
            @else
                <form wire:submit.prevent="receive">
                    <div class="border-b border-slate-100 bg-slate-50/70 px-6 py-4">
                        <div class="grid gap-4 xl:grid-cols-[1.1fr_0.9fr]">
                            <div class="rounded-2xl border border-slate-200 bg-white px-4 py-4 shadow-sm">
                                <label class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Entité qui reçoit la marchandise</label>
                                <select wire:model.defer="receive_location_id" class="input mt-2" @disabled(!$canSelectAnyLocation)>
                                    <option value="">Choisir dépôt ou magasin</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }} ({{ $location->code }})</option>
                                    @endforeach
                                </select>
                                <div class="mt-2 text-xs text-slate-500">Le stock sera ajouté uniquement dans cet emplacement.</div>
                                @error('receive_location_id') <span class="mt-2 block text-red-600 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="rounded-2xl border border-dashed border-cyan-200 bg-cyan-50/70 px-4 py-4">
                                <div class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">Contrôle réception</div>
                                <div class="mt-2 text-sm text-slate-600">
                                    Vérifie les quantités réellement reçues avant de stocker. Une fois approvisionné, le mouvement d'entrée n'est plus rejoué.
                                </div>
                                <div class="mt-3 flex flex-wrap gap-2 text-xs font-medium text-slate-600">
                                    <span class="rounded-full bg-white px-3 py-1 shadow-sm ring-1 ring-cyan-100">Commandé: {{ number_format($orderedQty, 3) }}</span>
                                    <span class="rounded-full bg-white px-3 py-1 shadow-sm ring-1 ring-cyan-100">Reçu: {{ number_format($receivedQty, 3) }}</span>
                                    <span class="rounded-full bg-white px-3 py-1 shadow-sm ring-1 ring-cyan-100">Reste: {{ number_format($pendingQty, 3) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-slate-50/80">
                                <tr class="text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                                    <th class="px-6 py-3">Article</th>
                                    <th class="px-6 py-3 text-right">Qté commandée</th>
                                    <th class="px-6 py-3 text-right">Qté réceptionnée</th>
                                    <th class="px-6 py-3 text-right">Reste</th>
                                    <th class="px-6 py-3 text-right">Coût unitaire</th>
                                    <th class="px-6 py-3 text-right">Total ligne</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($purchaseOrder->items as $item)
                                    @php
                                        $receivedLineQty = (float) ($item->received_quantity ?? 0);
                                        $pendingLineQty = max(0, (float) $item->quantity - $receivedLineQty);
                                    @endphp
                                    <tr class="hover:bg-slate-50/70">
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-slate-900">{{ $item->product->name }}</div>
                                            <div class="mt-1 text-xs text-slate-500">
                                                {{ \Illuminate\Support\Str::limit($item->product->description ?: "Coût moyen unitaire calculé pour l'entrée en stock.", 72) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right font-medium text-slate-700">{{ number_format((float) $item->quantity, 3) }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <input
                                                wire:model.defer="receivedQuantities.{{ $item->id }}"
                                                type="number"
                                                min="0"
                                                step="0.001"
                                                class="input ml-auto max-w-[140px] text-right"
                                            >
                                        </td>
                                        <td class="px-6 py-4 text-right font-medium text-slate-700">{{ number_format($pendingLineQty, 3) }}</td>
                                        <td class="px-6 py-4 text-right font-medium text-slate-700">{{ number_format((float) $item->unit_cost_local, 2) }}</td>
                                        <td class="px-6 py-4 text-right font-semibold text-slate-900">{{ number_format((float) $item->line_total_local, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($purchaseOrder->status !== 'approvisionnee')
                        <div class="border-t border-slate-100 px-6 py-4 flex justify-end">
                            <button class="btn btn-primary" type="submit">Marquer réception et stocker</button>
                        </div>
                    @endif
                </form>
            @endif
        </div>

        <div class="space-y-6">
            <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Résumé financier</h2>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Sous-total local</span>
                        <span class="font-semibold text-slate-900">{{ number_format((float) $purchaseOrder->subtotal_local, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Sous-total devise</span>
                        <span class="font-semibold text-slate-900">{{ number_format((float) $purchaseOrder->subtotal_foreign, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Frais accessoires</span>
                        <span class="font-semibold text-slate-900">{{ number_format((float) $purchaseOrder->accessory_fees_local, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Transport</span>
                        <span class="font-semibold text-slate-900">{{ number_format((float) $purchaseOrder->transport_fees_local, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                        <span class="text-slate-500">Total coût local</span>
                        <span class="text-lg font-semibold text-slate-900">{{ number_format((float) $purchaseOrder->total_cost_local, 2) }}</span>
                    </div>
                    @if ($purchaseOrder->type === 'foreign')
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Transferts devise</span>
                            <span class="font-semibold text-slate-900">{{ number_format($transferredForeign, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Transferts local</span>
                            <span class="font-semibold text-slate-900">{{ number_format($transferredLocal, 2) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Informations achat</h2>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Devise</span>
                        <span class="font-semibold text-slate-900">{{ $purchaseOrder->currency }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Taux de change</span>
                        <span class="font-semibold text-slate-900">{{ number_format((float) $purchaseOrder->exchange_rate, 6) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">En transit le</span>
                        <span class="font-semibold text-slate-900">{{ $purchaseOrder->in_transit_at?->format('d/m/Y') ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Pièces jointes</span>
                        <span class="font-semibold text-slate-900">{{ number_format($attachmentsCount) }}</span>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <span class="text-slate-500">Notes</span>
                        <span class="max-w-[65%] text-right font-medium text-slate-900 whitespace-pre-line">{{ $purchaseOrder->notes ?: 'Aucune note' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($purchaseOrder->type === 'foreign')
        <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Transferts fournisseur</h2>
                <p class="mt-1 text-sm text-slate-500">Suivi des paiements ou transferts liés à cet achat import.</p>
            </div>

            <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr] p-6">
                <form wire:submit.prevent="addTransfer" class="space-y-4 rounded-3xl border border-slate-200 bg-slate-50/70 p-5">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Nouveau transfert</div>
                        <p class="mt-1 text-sm text-slate-500">Enregistre un paiement fournisseur ou un transfert lié à cet achat import.</p>
                    </div>
                    <div class="grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Montant devise</label>
                            <input wire:model.defer="amount_foreign" type="number" step="0.01" class="input mt-2" placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Montant local</label>
                            <input wire:model.defer="amount_local" type="number" step="0.01" class="input mt-2" placeholder="0.00">
                        </div>
                    </div>
                    <div class="grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Date paiement</label>
                            <input wire:model.defer="paid_at" type="date" class="input mt-2">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Référence</label>
                            <input wire:model.defer="reference" class="input mt-2" placeholder="Référence">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Notes</label>
                        <textarea wire:model.defer="notes" rows="4" class="input mt-2" placeholder="Notes"></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button class="btn btn-primary" type="submit">Ajouter transfert</button>
                    </div>
                </form>

                <div class="rounded-3xl border border-slate-200 bg-white overflow-hidden">
                    @if ($purchaseOrder->transfers->isEmpty())
                        <div class="px-6 py-10 text-sm text-slate-500">Aucun transfert enregistré.</div>
                    @else
                        <table class="min-w-full">
                            <thead class="bg-slate-50/80">
                                <tr class="text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3 text-right">Devise</th>
                                    <th class="px-4 py-3 text-right">Local</th>
                                    <th class="px-4 py-3">Référence</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($purchaseOrder->transfers as $transfer)
                                    <tr>
                                        <td class="px-4 py-4 text-sm text-slate-600">{{ $transfer->paid_at?->format('d/m/Y') ?? '—' }}</td>
                                        <td class="px-4 py-4 text-right font-medium text-slate-700">{{ number_format((float) $transfer->amount_foreign, 2) }}</td>
                                        <td class="px-4 py-4 text-right font-medium text-slate-700">{{ number_format((float) $transfer->amount_local, 2) }}</td>
                                        <td class="px-4 py-4 text-sm text-slate-600">{{ $transfer->reference ?: '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-lg font-semibold text-slate-900">Pièces jointes</h2>
            <p class="mt-1 text-sm text-slate-500">Factures, bons, images ou autres documents liés à l'achat.</p>
        </div>

        <div class="grid gap-6 xl:grid-cols-[0.9fr_1.1fr] p-6">
            <form wire:submit.prevent="uploadAttachment" class="space-y-4 rounded-3xl border border-slate-200 bg-slate-50/70 p-5">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Nouvelle pièce jointe</div>
                    <p class="mt-1 text-sm text-slate-500">Ajoute un bon, une facture, une image ou un document de suivi.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Ajouter un fichier</label>
                    <input type="file" wire:model="attachment" class="input mt-2">
                    @error('attachment') <span class="mt-2 block text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end">
                    <button class="btn btn-primary" type="submit">Ajouter pièce</button>
                </div>
            </form>

            <div class="rounded-3xl border border-slate-200 bg-white">
                @if ($purchaseOrder->attachments->isEmpty())
                    <div class="px-6 py-10 text-sm text-slate-500">Aucune pièce jointe enregistrée.</div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach ($purchaseOrder->attachments as $file)
                            <div class="flex items-center justify-between gap-4 px-6 py-4">
                                <div>
                                    <div class="font-medium text-slate-900">{{ $file->original_name }}</div>
                                    <div class="mt-1 text-xs text-slate-500">{{ strtoupper((string) ($file->mime_type ?? 'fichier')) }} · {{ number_format(((float) $file->size) / 1024, 1) }} KB</div>
                                </div>
                                <button wire:click="downloadAttachment({{ $file->id }})" class="btn btn-secondary" type="button">
                                    Télécharger
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
