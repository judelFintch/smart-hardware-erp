<div class="form-section">
    <div class="section-header">
        <div>
            <div class="text-sm text-slate-500">Stock</div>
            <div class="text-lg font-semibold">Inventaire</div>
        </div>
        <a class="btn btn-secondary" href="{{ route('inventory-counts.index') }}" wire:navigate>Retour</a>
    </div>
    <form wire:submit.prevent="save" class="section-body space-y-6" data-autosave data-autosave-key="inventory-count-create">
        @include('livewire.partials.context-help', [
            'eyebrow' => 'Aide Inventaire',
            'title' => 'Faire un inventaire utile',
            'intro' => 'Un inventaire sert à comparer le stock système et le stock réellement compté. Plus la saisie est précise, plus la correction sera fiable.',
            'items' => [
                ['title' => 'Choisissez le bon lieu', 'text' => 'Chaque inventaire concerne un seul emplacement. Vérifiez bien le dépôt ou magasin avant de commencer.'],
                ['title' => 'Saisissez les quantités comptées', 'text' => 'Entrez la quantité réellement observée, même si elle diffère du stock attendu dans le système.'],
                ['title' => 'Ajoutez une note si nécessaire', 'text' => 'Une note aide à expliquer un écart, une casse, une perte ou une correction particulière.'],
            ],
            'actionRoute' => 'help.index',
            'actionLabel' => 'Ouvrir l’aide',
        ])

        <div class="form-grid">
            <div>
                <label class="block text-sm font-medium">Lieu</label>
                <select wire:model.defer="location_id" class="input" required @disabled(!$canSelectAnyLocation)>
                    <option value="">-- Choisir --</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
                @error('location_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Date</label>
                <input wire:model.defer="counted_at" type="date" class="input">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium">Notes</label>
            <textarea wire:model.defer="notes" rows="3" class="input"></textarea>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <div class="text-sm text-slate-500">Articles</div>
                    <div class="text-lg font-semibold">Quantités comptées</div>
                </div>
                <button type="button" wire:click="addItem" class="btn btn-secondary">Ajouter ligne</button>
            </div>
            <div class="card-body space-y-3">
                @error('items') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                @foreach ($items as $index => $item)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        <select wire:model.defer="items.{{ $index }}.product_id" class="input">
                            <option value="">-- Article --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <input wire:model.defer="items.{{ $index }}.counted_quantity" type="number" step="0.001" class="input" placeholder="Quantité comptée">
                        <button type="button" wire:click="removeItem({{ $index }})" class="btn btn-secondary">Supprimer</button>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a class="btn btn-secondary" href="{{ route('inventory-counts.index') }}" wire:navigate>Annuler</a>
            <button class="btn btn-primary" type="submit">Enregistrer inventaire</button>
        </div>
    </form>
</div>
