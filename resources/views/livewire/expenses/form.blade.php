<div>
    <h1 class="text-2xl font-semibold mb-4">{{ $title }}</h1>
    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <label class="block">Catégorie</label>
            <input wire:model.defer="category" class="border p-2 w-full">
        </div>
        <div>
            <label class="block">Description</label>
            <input wire:model.defer="description" class="border p-2 w-full" required>
            @error('description') <span class="text-red-600">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block">Montant</label>
            <input wire:model.defer="amount" type="number" step="0.01" class="border p-2 w-full" required>
            @error('amount') <span class="text-red-600">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block">Date</label>
            <input wire:model.defer="spent_at" type="date" class="border p-2 w-full">
        </div>
        <div>
            <label class="block">Référence</label>
            <input wire:model.defer="reference" class="border p-2 w-full">
        </div>
        <button class="px-3 py-2 bg-blue-600 text-white rounded" type="submit">Enregistrer</button>
    </form>
</div>
