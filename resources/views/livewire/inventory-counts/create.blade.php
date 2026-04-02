<div class="form-section">
    <div class="section-header">
        <div>
            <div class="text-sm text-slate-500">Stock</div>
            <div class="text-lg font-semibold">Inventaire</div>
        </div>
        <a class="btn btn-secondary" href="{{ route('inventory-counts.index') }}" wire:navigate>Retour</a>
    </div>
    <form wire:submit.prevent="save" class="section-body space-y-6" data-autosave data-autosave-key="inventory-count-create">
        @include('livewire.partials.context-help', [
            'eyebrow' => 'Aide Inventaire',
            'title' => 'Faire un inventaire utile',
            'intro' => 'Un inventaire sert à comparer le stock système et le stock réellement compté. Plus la saisie est précise, plus la correction sera fiable.',
            'items' => [
                ['title' => 'Choisissez le bon lieu', 'text' => 'Chaque inventaire concerne un seul emplacement. Vérifiez bien le dépôt ou magasin avant de commencer.'],
                ['title' => 'Saisissez les quantités comptées', 'text' => 'Entrez la quantité réellement observée, même si elle diffère du stock attendu dans le système.'],
                ['title' => 'Ajoutez une note si nécessaire', 'text' => 'Une note aide à expliquer un écart, une casse, une perte ou une correction particulière.'],
            ],
            'actionRoute' => 'help.index',
            'actionLabel' => 'Ouvrir l’aide',
        ])

        <div class="form-grid">
            <div>
                <label class="block text-sm font-medium">Lieu</label>
                <select wire:model.live="location_id" class="input" required @disabled(!$canSelectAnyLocation)>
                    <option value="">-- Choisir --</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
                @error('location_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Date</label>
                <input wire:model.defer="counted_at" type="date" class="input">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium">Notes</label>
            <textarea wire:model.defer="notes" rows="3" class="input"></textarea>
        </div>

        <div class="rounded-[24px] border border-slate-200 bg-white px-3 py-2.5 shadow-sm">
            <div class="grid gap-x-4 gap-y-1.5 md:grid-cols-3">
                <div class="flex min-w-0 items-baseline justify-between gap-2 border-b border-slate-100 py-1.5 md:border-b-0 md:border-r md:pr-3">
                    <span class="truncate text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Articles chargés</span>
                    <span class="shrink-0 text-sm font-semibold text-slate-900">{{ number_format(count($items)) }}</span>
                </div>
                <div class="flex min-w-0 items-baseline justify-between gap-2 border-b border-slate-100 py-1.5 md:border-b-0 md:border-r md:px-3">
                    <span class="truncate text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Quantités saisies</span>
                    <span class="shrink-0 text-sm font-semibold text-slate-900">{{ number_format($countedItems) }}</span>
                </div>
                <div class="flex min-w-0 items-baseline justify-between gap-2 py-1.5 md:pl-3">
                    <span class="truncate text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Écarts détectés</span>
                    <span class="shrink-0 text-sm font-semibold text-slate-900">{{ number_format($differencesCount) }}</span>
                </div>
            </div>
            <div class="mt-2 text-xs text-slate-500">
                Choisissez un lieu pour charger automatiquement les articles déjà présents dans cet emplacement.
            </div>
        </div>

        <div class="rounded-[24px] border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid gap-3 lg:grid-cols-[1fr_auto] lg:items-end">
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400">Recherche</label>
                    <input wire:model.live.debounce.200ms="search" class="input mt-2" placeholder="Nom ou SKU de l'article">
                </div>
                <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-600">
                    <input wire:model.live="showOnlyCounted" type="checkbox" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                    Afficher seulement les lignes saisies
                </label>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <div class="text-sm text-slate-500">Articles</div>
                    <div class="text-lg font-semibold">Saisie rapide des quantités comptées</div>
                </div>
            </div>
            <div class="card-body space-y-3">
                @error('items') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror

                @if (!$location_id)
                    <div class="rounded-xl border border-dashed border-slate-300 px-4 py-4 text-sm text-slate-500">
                        Choisissez d'abord un lieu pour charger les articles à inventorier.
                    </div>
                @elseif (count($items) === 0)
                    <div class="rounded-xl border border-dashed border-slate-300 px-4 py-4 text-sm text-slate-500">
                        Aucun article trouvé dans cet emplacement pour le moment.
                    </div>
                @elseif ($visibleItems->isEmpty())
                    <div class="rounded-xl border border-dashed border-slate-300 px-4 py-4 text-sm text-slate-500">
                        Aucun article ne correspond au filtre actuel.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-slate-50/80">
                                <tr class="text-left text-[11px] uppercase tracking-[0.14em] text-slate-500">
                                    <th class="px-4 py-3">Article</th>
                                    <th class="px-4 py-3 text-right">Stock système</th>
                                    <th class="px-4 py-3 text-right">Qté comptée</th>
                                    <th class="px-4 py-3 text-right">Écart</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($visibleItems as $index => $item)
                                    @php
                                        $systemQuantity = (float) ($item['system_quantity'] ?? 0);
                                        $countedQuantity = $item['counted_quantity'];
                                        $difference = ($countedQuantity !== null && $countedQuantity !== '') ? ((float) $countedQuantity - $systemQuantity) : null;
                                    @endphp
                                    <tr class="hover:bg-slate-50/70">
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-slate-900">{{ $item['product_name'] }}</div>
                                            <div class="mt-1 text-xs text-slate-500">{{ $item['product_sku'] ?: 'Sans SKU' }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm font-semibold text-slate-700">{{ number_format($systemQuantity, 3) }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <input
                                                wire:model.live.debounce.250ms="items.{{ $index }}.counted_quantity"
                                                type="number"
                                                step="0.001"
                                                min="0"
                                                class="input inline-flex w-32 text-right"
                                                placeholder="0"
                                            >
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm font-semibold {{ $difference === null ? 'text-slate-400' : ($difference == 0.0 ? 'text-emerald-600' : 'text-amber-700') }}">
                                            @if ($difference === null)
                                                —
                                            @else
                                                {{ $difference > 0 ? '+' : '' }}{{ number_format($difference, 3) }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a class="btn btn-secondary" href="{{ route('inventory-counts.index') }}" wire:navigate>Annuler</a>
            <button class="btn btn-primary" type="submit">Enregistrer inventaire</button>
        </div>
    </form>
</div>
