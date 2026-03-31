<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <div class="text-sm text-slate-500">Stock</div>
            <div class="text-lg font-semibold text-slate-900">Transfert</div>
        </div>
        <a class="btn btn-secondary" href="{{ route('stock-movements.index') }}" wire:navigate>Retour</a>
    </div>

    <form wire:submit.prevent="save" class="space-y-4" data-autosave data-autosave-key="stock-transfer-create">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid gap-3 md:grid-cols-[1fr_auto_1fr] md:items-end">
                <div>
                    <label class="mb-1 block text-sm font-medium">Source</label>
                    <select wire:model.live="from_location_id" class="input" required @disabled(!$canSelectAnyLocation)>
                        <option value="">-- Choisir --</option>
                        @foreach ($fromLocations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                    @error('from_location_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-center pb-2 text-sm font-semibold text-cyan-700">
                    <span class="rounded-full bg-cyan-50 px-3 py-2">→</span>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Destination</label>
                    <select wire:model.live="to_location_id" class="input" required>
                        <option value="">-- Choisir --</option>
                        @foreach ($toLocations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                    @error('to_location_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-3 rounded-xl bg-slate-50 px-3 py-2 text-xs text-slate-600">
                @if ($fromLocation && $toLocation)
                    Mouvement prévu : <span class="font-semibold text-slate-900">{{ $fromLocation->name }}</span> vers <span class="font-semibold text-slate-900">{{ $toLocation->name }}</span>.
                @elseif ($fromLocation)
                    Sélectionne la destination. Les articles affichés viennent uniquement du stock disponible dans <span class="font-semibold text-slate-900">{{ $fromLocation->name }}</span>.
                @else
                    Choisis d’abord l’entité source pour filtrer les articles transférables.
                @endif
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                <div>
                    <div class="text-sm font-semibold text-slate-900">Lignes de transfert</div>
                    <div class="text-xs text-slate-500">Une ligne par article, sur un seul rang.</div>
                </div>
                <button type="button" wire:click="addItem" class="btn btn-secondary">Ajouter</button>
            </div>

            <div class="space-y-2 p-3">
                @error('items') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror

                <div class="hidden grid-cols-[minmax(0,1.8fr)_140px_120px_110px] gap-2 px-1 text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-400 md:grid">
                    <div>Article</div>
                    <div>Disponible</div>
                    <div>Quantité</div>
                    <div></div>
                </div>

                @foreach ($items as $index => $item)
                    @php
                        $selectedProductId = (int) ($item['product_id'] ?? 0);
                        $availableForLine = (float) ($availableQuantities[$selectedProductId] ?? 0);
                    @endphp

                    <div class="grid grid-cols-1 gap-2 md:grid-cols-[minmax(0,1.8fr)_140px_120px_110px] md:items-center" wire:key="transfer-item-{{ $index }}">
                        <select wire:model.live="items.{{ $index }}.product_id" class="input" @disabled(blank($from_location_id))>
                            <option value="">-- Article --</option>
                            @foreach ($availableProducts as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>

                        <div class="input flex items-center bg-slate-50 text-sm text-slate-600">
                            {{ number_format($availableForLine, 3) }}
                        </div>

                        <input wire:model.defer="items.{{ $index }}.quantity" type="number" step="0.001" class="input" placeholder="Qté">

                        <button type="button" wire:click="removeItem({{ $index }})" class="btn btn-secondary">Supprimer</button>
                    </div>
                @endforeach

                @if ($from_location_id && $availableProducts->isEmpty())
                    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        Aucun article transférable pour cette source.
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a class="btn btn-secondary" href="{{ route('stock-movements.index') }}" wire:navigate>Annuler</a>
            <button class="btn btn-primary" type="submit">Transférer</button>
        </div>
    </form>
</div>
