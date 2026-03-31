@if ($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 px-4">
        <div class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-6 shadow-xl">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-red-600">Suppression protégée</div>
            <h2 class="mt-2 text-xl font-semibold text-slate-900">Confirmer la suppression</h2>
            <p class="mt-2 text-sm text-slate-600">
                Saisis le code secret de ton compte pour supprimer
                <span class="font-semibold text-slate-900">{{ $pendingDeleteLabel ?: 'cet élément' }}</span>.
            </p>

            <div class="mt-5">
                <label class="block text-sm font-medium text-slate-700">Code secret</label>
                <input
                    wire:model.defer="deleteSecretCode"
                    type="password"
                    class="input mt-2"
                    placeholder="Entrer le code secret"
                >
                @error('deleteSecretCode') <span class="mt-2 block text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mt-6 flex items-center justify-end gap-2">
                <button type="button" class="btn btn-secondary" wire:click="cancelDelete">Annuler</button>
                <button type="button" class="btn btn-primary" wire:click="confirmDelete">Supprimer</button>
            </div>
        </div>
    </div>
@endif
