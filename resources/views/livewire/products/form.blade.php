<div class="form-section">
    <div class="section-header">
        <div>
            <div class="text-sm text-slate-500">Article</div>
            <div class="text-lg font-semibold">{{ $title }}</div>
        </div>
        <a class="btn btn-secondary" href="{{ route('products.index') }}" wire:navigate>Retour</a>
    </div>
    <form wire:submit.prevent="save" class="section-body space-y-6">
        <div class="form-grid">
            <div>
                <label class="block text-sm font-medium">SKU</label>
                <input wire:model.defer="sku" class="input" required>
                @error('sku') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Nom</label>
                <input wire:model.defer="name" class="input" required>
                @error('name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Unité</label>
                <select wire:model.defer="unit_id" class="input" required>
                    <option value="">-- Choisir --</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }} ({{ strtoupper($unit->code) }})</option>
                    @endforeach
                </select>
                @error('unit_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Marge (%)</label>
                <input wire:model.defer="sale_margin_percent" type="number" step="0.01" class="input">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium">Description</label>
            <textarea wire:model.defer="description" rows="4" class="input"></textarea>
        </div>
        <div class="flex items-center justify-end gap-2">
            <a class="btn btn-secondary" href="{{ route('products.index') }}" wire:navigate>Annuler</a>
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>
</div>
