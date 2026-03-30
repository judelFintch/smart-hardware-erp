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
                <select wire:model.defer="from_location_id" class="input" required @disabled(!$canSelectAnyLocation)>
                    <option value="">-- Choisir --</option>
                    @foreach ($fromLocations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
                @error('from_location_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Vers</label>
                <select wire:model.defer="to_location_id" class="input" required>
                    <option value="">-- Choisir --</option>
                    @foreach ($toLocations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
                @error('to_location_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <div class="text-sm text-slate-500">Articles</div>
                    <div class="text-lg font-semibold">Quantités à transférer</div>
                </div>
                <button type="button" wire:click="addItem" class="btn btn-secondary">Ajouter ligne</button>
            </div>
            <div class="card-body space-y-3">
                @error('items') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                @foreach ($items as $index => $item)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        <select wire:model.defer="items.{{ $index }}.product_id" class="input">
                            <option value="">-- Article --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <input wire:model.defer="items.{{ $index }}.quantity" type="number" step="0.001" class="input" placeholder="Quantité">
                        <button type="button" wire:click="removeItem({{ $index }})" class="btn btn-secondary">Supprimer</button>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="flex items-center justify-end gap-2">
            <a class="btn btn-secondary" href="{{ route('stock-movements.index') }}" wire:navigate>Annuler</a>
            <button class="btn btn-primary" type="submit">Transférer</button>
        </div>
    </form>
</div>
