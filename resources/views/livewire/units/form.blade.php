<div class="form-section">
    <div class="section-header">
        <div>
            <div class="text-sm text-slate-500">Unité</div>
            <div class="text-lg font-semibold">{{ $title }}</div>
        </div>
        <a class="btn btn-secondary" href="{{ route('units.index') }}" wire:navigate>Retour</a>
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
            <div>
                <label class="block text-sm font-medium">Type</label>
                <select wire:model.defer="type" class="input" required>
                    <option value="piece">Pièce</option>
                    <option value="weight">Poids (kg, g)</option>
                    <option value="volume">Volume (L)</option>
                    <option value="other">Autre</option>
                </select>
            </div>
        </div>
        <div class="flex items-center justify-end gap-2">
            <a class="btn btn-secondary" href="{{ route('units.index') }}" wire:navigate>Annuler</a>
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>
</div>
