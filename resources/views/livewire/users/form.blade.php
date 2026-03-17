<div class="form-section">
    <div class="section-header">
        <div>
            <div class="text-sm text-slate-500">Utilisateur</div>
            <div class="text-lg font-semibold">{{ $title }}</div>
        </div>
        <a class="btn btn-secondary" href="{{ route('users.index') }}" wire:navigate>Retour</a>
    </div>
    <form wire:submit.prevent="save" class="section-body space-y-6" data-autosave data-autosave-key="user-form-{{ $user?->id ?? 'new' }}">
        <div class="form-grid">
            <div>
                <label class="block text-sm font-medium">Nom</label>
                <input wire:model.defer="name" class="input" required>
                @error('name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Email</label>
                <input wire:model.defer="email" type="email" class="input" required>
                @error('email') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Rôle</label>
                <select wire:model.defer="role" class="input" required>
                    <option value="owner">Owner</option>
                    <option value="manager">Manager</option>
                    <option value="seller">Seller</option>
                </select>
            </div>
        </div>

        <div class="form-grid">
            <div>
                <label class="block text-sm font-medium">Mot de passe</label>
                <input wire:model.defer="password" type="password" class="input" @if(!$user) required @endif>
                @error('password') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Confirmer</label>
                <input wire:model.defer="password_confirmation" type="password" class="input" @if(!$user) required @endif>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a class="btn btn-secondary" href="{{ route('users.index') }}" wire:navigate>Annuler</a>
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>
</div>
