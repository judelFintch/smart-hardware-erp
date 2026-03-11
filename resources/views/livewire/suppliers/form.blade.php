<div>
    <h1 class="text-2xl font-semibold mb-4">{{ $title }}</h1>
    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <label class="block">Nom</label>
            <input wire:model.defer="name" class="border p-2 w-full" required>
            @error('name') <span class="text-red-600">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block">Type</label>
            <select wire:model.defer="type" class="border p-2 w-full" required>
                <option value="local">Local</option>
                <option value="foreign">Étranger</option>
            </select>
        </div>
        <div>
            <label class="block">Téléphone</label>
            <input wire:model.defer="phone" class="border p-2 w-full">
        </div>
        <div>
            <label class="block">Email</label>
            <input wire:model.defer="email" type="email" class="border p-2 w-full">
        </div>
        <div>
            <label class="block">Adresse</label>
            <input wire:model.defer="address" class="border p-2 w-full">
        </div>
        <div>
            <label class="block">Notes</label>
            <textarea wire:model.defer="notes" class="border p-2 w-full"></textarea>
        </div>
        <button class="px-3 py-2 bg-blue-600 text-white rounded" type="submit">Enregistrer</button>
    </form>
</div>
