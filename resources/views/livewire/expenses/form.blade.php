<div class="form-section">
    <div class="section-header">
        <div>
            <div class="text-sm text-slate-500">Dépense</div>
            <div class="text-lg font-semibold">{{ $title }}</div>
        </div>
        <a class="btn btn-secondary" href="{{ route('expenses.index') }}" wire:navigate>Retour</a>
    </div>
    <form wire:submit.prevent="save" class="section-body space-y-6">
        <div class="form-grid">
            <div>
                <label class="block text-sm font-medium">Catégorie</label>
                <input wire:model.defer="category" class="input">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium">Description</label>
                <input wire:model.defer="description" class="input" required>
                @error('description') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Montant</label>
                <input wire:model.defer="amount" type="number" step="0.01" class="input" required>
                @error('amount') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Date</label>
                <input wire:model.defer="spent_at" type="date" class="input">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium">Référence</label>
                <input wire:model.defer="reference" class="input">
            </div>
        </div>
        <div class="flex items-center justify-end gap-2">
            <a class="btn btn-secondary" href="{{ route('expenses.index') }}" wire:navigate>Annuler</a>
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>
</div>
