<div>
    <h1 class="text-2xl font-semibold mb-4">Transfert dépôt → magasin</h1>
    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <h2 class="text-lg font-semibold mb-2">Articles</h2>
            @error('items') <span class="text-red-600">{{ $message }}</span> @enderror
            <div class="space-y-2">
                @foreach ($items as $index => $item)
                    <div class="grid grid-cols-3 gap-2">
                        <select wire:model.defer="items.{{ $index }}.product_id" class="border p-2">
                            <option value="">-- Article --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <input wire:model.defer="items.{{ $index }}.quantity" type="number" step="0.001" class="border p-2" placeholder="Quantité">
                        <button type="button" wire:click="removeItem({{ $index }})" class="px-2 bg-gray-200">X</button>
                    </div>
                @endforeach
            </div>
            <button type="button" wire:click="addItem" class="mt-2 px-3 py-2 bg-gray-700 text-white rounded">Ajouter ligne</button>
        </div>
        <button class="px-3 py-2 bg-blue-600 text-white rounded" type="submit">Transférer</button>
    </form>
</div>
