<div class="form-section">
    <div class="section-header">
        <div>
            <div class="text-sm text-slate-500">Stock</div>
            <div class="text-lg font-semibold">Transfert entre magasins</div>
        </div>
        <a class="btn btn-secondary" href="{{ route('stock-movements.index') }}" wire:navigate>Retour</a>
    </div>
    <form wire:submit.prevent="save" class="section-body space-y-6" data-autosave data-autosave-key="stock-transfer-create">
        <div class="form-grid">
            <div>
                <label class="block text-sm font-medium">De</label>
                <select wire:model.live="from_location_id" class="input" required @disabled(!$canSelectAnyLocation)>
                    <option value="">-- Choisir --</option>
                    @foreach ($fromLocations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
                @error('from_location_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Vers</label>
                <select wire:model.live="to_location_id" class="input" required>
                    <option value="">-- Choisir --</option>
                    @foreach ($toLocations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
                @error('to_location_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="rounded-2xl border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm text-cyan-900">
            <div class="font-semibold">Sens du transfert</div>
            <div class="mt-1">
                @if ($fromLocation && $toLocation)
                    Le stock sera retiré de <span class="font-semibold">{{ $fromLocation->name }}</span> puis ajouté à <span class="font-semibold">{{ $toLocation->name }}</span>.
                @elseif ($fromLocation)
                    Sélectionne la destination pour confirmer le sens du mouvement depuis <span class="font-semibold">{{ $fromLocation->name }}</span>.
                @else
                    Choisis d’abord l’entité source. La liste des articles affichera uniquement les références avec un stock strictement positif dans cette entité.
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <div class="text-sm text-slate-500">Articles</div>
                    <div class="text-lg font-semibold">Quantités à transférer</div>
                    <div class="text-xs text-slate-500">Seuls les articles disponibles dans le magasin source sont proposés.</div>
                </div>
                <button type="button" wire:click="addItem" class="btn btn-secondary">Ajouter ligne</button>
            </div>
            <div class="card-body space-y-3">
                @error('items') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                @foreach ($items as $index => $item)
                    <div class="grid grid-cols-1 gap-2 md:grid-cols-[1.5fr_0.9fr_0.7fr_0.7fr]" wire:key="transfer-item-{{ $index }}">
                        <select wire:model.live="items.{{ $index }}.product_id" class="input" @disabled(blank($from_location_id))>
                            <option value="">-- Article --</option>
                            @foreach ($availableProducts as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} (stock: {{ number_format((float) ($availableQuantities[$product->id] ?? 0), 3) }})</option>
                            @endforeach
                        </select>
                        <div class="input bg-slate-50 text-sm text-slate-600">
                            @php
                                $selectedProductId = (int) ($item['product_id'] ?? 0);
                                $availableForLine = (float) ($availableQuantities[$selectedProductId] ?? 0);
                            @endphp
                            Disponible: {{ number_format($availableForLine, 3) }}
                        </div>
                        <input wire:model.defer="items.{{ $index }}.quantity" type="number" step="0.001" class="input" placeholder="Quantité">
                        <button type="button" wire:click="removeItem({{ $index }})" class="btn btn-secondary">Supprimer</button>
                    </div>
                @endforeach
                @if ($from_location_id && $availableProducts->isEmpty())
                    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        Aucun article transférable pour cette entité source. Le stock disponible doit être strictement supérieur à 0.
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
