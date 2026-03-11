<div class="form-section">
    <div class="section-header">
        <div>
            <div class="text-sm text-slate-500">Achats</div>
            <div class="text-lg font-semibold">Nouvel Achat</div>
        </div>
        <a class="btn btn-secondary" href="{{ route('purchases.index') }}" wire:navigate>Retour</a>
    </div>
    <form wire:submit.prevent="save" class="section-body space-y-6">
        <div class="form-grid">
            <div>
                <label class="block text-sm font-medium">Fournisseur</label>
                <select wire:model.defer="supplier_id" class="input" required>
                    <option value="">-- Sélectionner --</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }} ({{ $supplier->type }})</option>
                    @endforeach
                </select>
                @error('supplier_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Type</label>
                <select wire:model.defer="type" class="input" required>
                    <option value="local">Local</option>
                    <option value="foreign">Étranger</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium">Statut</label>
                <select wire:model.defer="status" class="input" required>
                    <option value="en_cours">En cours</option>
                    <option value="en_transit">En transit</option>
                    <option value="receptionnee">Réceptionnée</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium">Lieu de réception</label>
                <select wire:model.defer="receive_location_id" class="input">
                    <option value="">-- Dépôt par défaut --</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
                @error('receive_location_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Référence</label>
                <input wire:model.defer="reference" class="input">
            </div>
            <div>
                <label class="block text-sm font-medium">Commande le</label>
                <input wire:model.defer="ordered_at" type="date" class="input">
            </div>
            <div>
                <label class="block text-sm font-medium">En transit le</label>
                <input wire:model.defer="in_transit_at" type="date" class="input">
            </div>
            <div>
                <label class="block text-sm font-medium">Réception le</label>
                <input wire:model.defer="received_at" type="date" class="input">
            </div>
            <div>
                <label class="block text-sm font-medium">Devise</label>
                <input wire:model.defer="currency" class="input">
            </div>
            <div>
                <label class="block text-sm font-medium">Taux de change</label>
                <input wire:model.defer="exchange_rate" type="number" step="0.000001" class="input">
            </div>
            <div>
                <label class="block text-sm font-medium">Frais accessoires (local)</label>
                <input wire:model.defer="accessory_fees_local" type="number" step="0.01" class="input">
            </div>
            <div>
                <label class="block text-sm font-medium">Frais transport (local)</label>
                <input wire:model.defer="transport_fees_local" type="number" step="0.01" class="input">
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <div class="text-sm text-slate-500">Articles</div>
                    <div class="text-lg font-semibold">Lignes d'achat</div>
                </div>
                <button type="button" wire:click="addItem" class="btn btn-secondary">Ajouter ligne</button>
            </div>
            <div class="card-body space-y-3">
                @error('items') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                @foreach ($items as $index => $item)
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                        <select wire:model.defer="items.{{ $index }}.product_id" class="input">
                            <option value="">-- Article --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <input wire:model.defer="items.{{ $index }}.quantity" type="number" step="0.001" class="input" placeholder="Quantité">
                        <input wire:model.defer="items.{{ $index }}.unit_price" type="number" step="0.01" class="input" placeholder="Prix unitaire">
                        <button type="button" wire:click="removeItem({{ $index }})" class="btn btn-secondary">Supprimer</button>
                    </div>
                @endforeach
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium">Notes</label>
            <textarea wire:model.defer="notes" rows="4" class="input"></textarea>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a class="btn btn-secondary" href="{{ route('purchases.index') }}" wire:navigate>Annuler</a>
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>
</div>
