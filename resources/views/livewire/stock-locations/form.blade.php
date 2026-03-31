<div class="form-section">
    <div class="section-header">
        <div>
            <div class="text-sm text-slate-500">Magasin / Dépôt</div>
            <div class="text-lg font-semibold">{{ $title }}</div>
        </div>
        <a class="btn btn-secondary" href="{{ route('stock-locations.index') }}" wire:navigate>Retour</a>
    </div>
    <form wire:submit.prevent="save" class="section-body space-y-6" data-autosave data-autosave-key="stock-location-form-{{ $location?->id ?? 'new' }}">
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
        <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
            <input wire:model.defer="is_default_sale" type="checkbox" class="mt-1 h-4 w-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
            <span>
                <span class="block text-sm font-medium text-slate-900">Entité de vente par défaut</span>
                <span class="block text-sm text-slate-500">Cette entité sera préselectionnée lors de la création d’une vente si l’utilisateur n’a pas déjà une entité affectée.</span>
            </span>
        </label>
        <div class="flex items-center justify-end gap-2">
            <a class="btn btn-secondary" href="{{ route('stock-locations.index') }}" wire:navigate>Annuler</a>
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>
</div>
