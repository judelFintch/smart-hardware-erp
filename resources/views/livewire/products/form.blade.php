<div>
    <h1 class="text-2xl font-semibold mb-4">{{ $title }}</h1>
    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <label class="block">SKU</label>
            <input wire:model.defer="sku" class="border p-2 w-full" required>
            @error('sku') <span class="text-red-600">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block">Nom</label>
            <input wire:model.defer="name" class="border p-2 w-full" required>
            @error('name') <span class="text-red-600">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block">Unité</label>
            <input wire:model.defer="unit" class="border p-2 w-full">
        </div>
        <div>
            <label class="block">Marge (%)</label>
            <input wire:model.defer="sale_margin_percent" type="number" step="0.01" class="border p-2 w-full">
        </div>
        <div>
            <label class="block">Description</label>
            <textarea wire:model.defer="description" class="border p-2 w-full"></textarea>
        </div>
        <button class="px-3 py-2 bg-blue-600 text-white rounded" type="submit">Enregistrer</button>
    </form>
</div>
