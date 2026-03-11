<div class="form-section">
    <div class="section-header">
        <div>
            <div class="text-sm text-slate-500">Entreprise</div>
            <div class="text-lg font-semibold">Configuration</div>
        </div>
        <button class="btn btn-primary" wire:click="save" type="button">Enregistrer</button>
    </div>
    <div class="section-body space-y-6">
        <div class="form-grid">
            <div>
                <label class="block text-sm font-medium">Nom</label>
                <input wire:model.defer="name" class="input" required>
                @error('name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Raison sociale</label>
                <input wire:model.defer="legal_name" class="input">
            </div>
            <div>
                <label class="block text-sm font-medium">Identifiant fiscal</label>
                <input wire:model.defer="tax_id" class="input">
            </div>
            <div>
                <label class="block text-sm font-medium">Téléphone</label>
                <input wire:model.defer="phone" class="input">
            </div>
            <div>
                <label class="block text-sm font-medium">Email</label>
                <input wire:model.defer="email" type="email" class="input">
            </div>
            <div>
                <label class="block text-sm font-medium">Adresse</label>
                <input wire:model.defer="address" class="input">
            </div>
            <div>
                <label class="block text-sm font-medium">Devise</label>
                <input wire:model.defer="currency" class="input">
            </div>
            <div>
                <label class="block text-sm font-medium">Logo</label>
                <input wire:model="logo" type="file" class="input">
                @error('logo') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium">Pied de facture</label>
            <textarea wire:model.defer="invoice_footer" rows="4" class="input"></textarea>
        </div>
        <div class="text-sm text-emerald-600" wire:loading.remove wire:target="save" x-data @saved.window="setTimeout(() => $el.classList.add('hidden'), 2000)">
            Paramètres enregistrés.
        </div>
    </div>
</div>
