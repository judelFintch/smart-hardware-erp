<div class="form-section">
    <div class="section-header">
        <div>
            <div class="text-sm text-slate-500">Magasin / Dépôt</div>
            <div class="text-lg font-semibold">{{ $title }}</div>
        </div>
        <a class="btn btn-secondary" href="{{ route('stock-locations.index') }}" wire:navigate>Retour</a>
    </div>
    <form wire:submit.prevent="save" class="section-body space-y-6">
        <div class="form-grid">
            <div>
                <label class="block text-sm font-medium">Code</label>
                <input wire:model.defer="code" class="input" required>
                @error('code') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Nom</label>
                <input wire:model.defer="name" class="input" required>
                @error('name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium">Notes</label>
            <textarea wire:model.defer="notes" rows="4" class="input"></textarea>
        </div>
        <div class="flex items-center justify-end gap-2">
            <a class="btn btn-secondary" href="{{ route('stock-locations.index') }}" wire:navigate>Annuler</a>
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>
</div>
