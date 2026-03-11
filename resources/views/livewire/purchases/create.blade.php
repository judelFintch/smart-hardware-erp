<div>
    <h1 class="text-2xl font-semibold mb-4">Nouvel Achat</h1>
    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <label class="block">Fournisseur</label>
            <select wire:model.defer="supplier_id" class="border p-2 w-full" required>
                <option value="">-- Sélectionner --</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->name }} ({{ $supplier->type }})</option>
                @endforeach
            </select>
            @error('supplier_id') <span class="text-red-600">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block">Type</label>
            <select wire:model.defer="type" class="border p-2 w-full" required>
                <option value="local">Local</option>
                <option value="foreign">Étranger</option>
            </select>
        </div>
        <div>
            <label class="block">Statut</label>
            <select wire:model.defer="status" class="border p-2 w-full" required>
                <option value="en_cours">En cours</option>
                <option value="en_transit">En transit</option>
                <option value="receptionnee">Réceptionnée</option>
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block">Commande le</label>
                <input wire:model.defer="ordered_at" type="date" class="border p-2 w-full">
            </div>
            <div>
                <label class="block">En transit le</label>
                <input wire:model.defer="in_transit_at" type="date" class="border p-2 w-full">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block">Réception le</label>
                <input wire:model.defer="received_at" type="date" class="border p-2 w-full">
            </div>
            <div>
                <label class="block">Référence</label>
                <input wire:model.defer="reference" class="border p-2 w-full">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block">Devise</label>
                <input wire:model.defer="currency" class="border p-2 w-full">
            </div>
            <div>
                <label class="block">Taux de change</label>
                <input wire:model.defer="exchange_rate" type="number" step="0.000001" class="border p-2 w-full">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block">Frais accessoires (local)</label>
                <input wire:model.defer="accessory_fees_local" type="number" step="0.01" class="border p-2 w-full">
            </div>
            <div>
                <label class="block">Frais transport (local)</label>
                <input wire:model.defer="transport_fees_local" type="number" step="0.01" class="border p-2 w-full">
            </div>
        </div>

        <div>
            <h2 class="text-lg font-semibold mb-2">Articles</h2>
            @error('items') <span class="text-red-600">{{ $message }}</span> @enderror
            <div class="space-y-2">
                @foreach ($items as $index => $item)
                    <div class="grid grid-cols-4 gap-2">
                        <select wire:model.defer="items.{{ $index }}.product_id" class="border p-2">
                            <option value="">-- Article --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <input wire:model.defer="items.{{ $index }}.quantity" type="number" step="0.001" class="border p-2" placeholder="Quantité">
                        <input wire:model.defer="items.{{ $index }}.unit_price" type="number" step="0.01" class="border p-2" placeholder="Prix unitaire">
                        <button type="button" wire:click="removeItem({{ $index }})" class="px-2 bg-gray-200">X</button>
                    </div>
                @endforeach
            </div>
            <button type="button" wire:click="addItem" class="mt-2 px-3 py-2 bg-gray-700 text-white rounded">Ajouter ligne</button>
        </div>

        <div>
            <label class="block">Notes</label>
            <textarea wire:model.defer="notes" class="border p-2 w-full"></textarea>
        </div>

        <button class="px-3 py-2 bg-blue-600 text-white rounded" type="submit">Enregistrer</button>
    </form>
</div>
