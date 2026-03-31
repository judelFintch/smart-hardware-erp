<div class="space-y-3">
    <div class="flex items-center justify-between">
        <div>
            <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Stock</div>
            <div class="text-xl font-semibold text-slate-900">Nouveau transfert</div>
        </div>
        <a class="btn btn-secondary" href="{{ route('stock-movements.index') }}" wire:navigate>Retour</a>
    </div>

    <form wire:submit.prevent="save" class="space-y-3" data-autosave data-autosave-key="stock-transfer-create">
        <div class="rounded-[24px] border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-4 py-3">
                <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_44px_minmax(0,1fr)_auto] md:items-end">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Source</label>
                        <select wire:model.live="from_location_id" class="input h-10" required @disabled(!$canSelectAnyLocation)>
                            <option value="">-- Choisir --</option>
                            @foreach ($fromLocations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                        @error('from_location_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="hidden items-center justify-center pb-2 md:flex">
                        <span class="grid h-10 w-10 place-items-center rounded-full border border-cyan-200 bg-cyan-50 text-sm font-semibold text-cyan-700">→</span>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Destination</label>
                        <select wire:model.live="to_location_id" class="input h-10" required>
                            <option value="">-- Choisir --</option>
                            @foreach ($toLocations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                        @error('to_location_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <button type="button" wire:click="addItem" class="btn btn-secondary h-10 md:min-w-[110px]">Ajouter</button>
                </div>
            </div>

            <div class="px-4 py-2">
                <div class="rounded-xl bg-slate-50 px-3 py-2 text-xs text-slate-600">
                    @if ($fromLocation && $toLocation)
                        <span class="font-medium text-slate-900">{{ $fromLocation->name }}</span>
                        <span class="mx-1 text-slate-400">→</span>
                        <span class="font-medium text-slate-900">{{ $toLocation->name }}</span>
                    @elseif ($fromLocation)
                        Articles filtrés selon le stock disponible dans <span class="font-medium text-slate-900">{{ $fromLocation->name }}</span>.
                    @else
                        Sélectionne une source pour afficher uniquement les articles transférables.
                    @endif
                </div>
            </div>

            <div class="border-t border-slate-100 px-3 py-3">
                @error('items') <div class="mb-2 text-xs text-red-600">{{ $message }}</div> @enderror

                <div class="hidden grid-cols-[170px_84px_84px_84px] gap-2 px-1 pb-1 text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-400 md:grid">
                    <div>Article</div>
                    <div>Stock</div>
                    <div>Qté</div>
                    <div></div>
                </div>

                <div class="space-y-2">
                    @foreach ($items as $index => $item)
                        @php
                            $selectedProductId = (int) ($item['product_id'] ?? 0);
                            $availableForLine = (float) ($availableQuantities[$selectedProductId] ?? 0);
                        @endphp

                        <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-2" wire:key="transfer-item-{{ $index }}">
                            <div class="grid grid-cols-1 gap-2 md:grid-cols-[170px_84px_84px_84px] md:items-center">
                                <select wire:model.live="items.{{ $index }}.product_id" class="input h-9 bg-white text-sm" @disabled(blank($from_location_id))>
                                    <option value="">-- Article --</option>
                                    @foreach ($availableProducts as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>

                                <div class="flex h-9 items-center rounded-lg border border-slate-200 bg-white px-2 text-sm text-slate-600">
                                    {{ number_format($availableForLine, 3) }}
                                </div>

                                <input wire:model.defer="items.{{ $index }}.quantity" type="number" step="0.001" class="input h-9 bg-white px-2 text-sm" placeholder="Qté">

                                <button type="button" wire:click="removeItem({{ $index }})" class="btn btn-secondary h-9 px-2 text-xs">Retirer</button>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($from_location_id && $availableProducts->isEmpty())
                    <div class="mt-2 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-800">
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
