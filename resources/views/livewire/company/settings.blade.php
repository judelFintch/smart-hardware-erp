<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-lime-50 to-cyan-50 p-6 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div class="max-w-3xl">
                <div class="inline-flex items-center rounded-full bg-lime-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-lime-700">
                    Entreprise
                </div>
                <h1 class="mt-3 text-3xl font-semibold text-slate-900">Configuration métier et technique</h1>
                <p class="mt-2 text-sm text-slate-500">Centralise les paramètres de devises, formats, numérotation des documents et seuil global d’alerte stock.</p>
            </div>
            <button class="btn btn-primary" wire:click="save" type="button">Enregistrer</button>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5">
                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Identité</div>
                <div class="mt-1 text-lg font-semibold text-slate-900">Informations générales</div>
            </div>
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
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-5">
                    <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Branding</div>
                    <div class="mt-1 text-lg font-semibold text-slate-900">Logo et édition</div>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium">Logo</label>
                        <input wire:model="logo" type="file" class="input">
                        @error('logo') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Pied de facture</label>
                        <textarea wire:model.defer="invoice_footer" rows="5" class="input"></textarea>
                    </div>
                </div>
            </div>

            <div class="rounded-[28px] border border-slate-200 bg-slate-950 p-6 text-white shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-300">Impact</div>
                <div class="mt-3 space-y-2 text-sm text-slate-300">
                    <div>Les changements ici influencent les documents, l’affichage et les alertes globales.</div>
                    <div>Le seuil stock bas s’applique si un article n’a pas de seuil spécifique.</div>
                </div>
            </div>
        </div>
    </div>

        <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5">
                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Exploitation</div>
                <div class="mt-1 text-lg font-semibold text-slate-900">Paramètres opérationnels</div>
            </div>
        <div class="form-grid">
            <div>
                <label class="block text-sm font-medium">Devise</label>
                <input wire:model.defer="currency" class="input">
            </div>
            <div>
                <label class="block text-sm font-medium">Symbole devise</label>
                <input wire:model.defer="currency_symbol" class="input">
            </div>
            <div>
                <label class="block text-sm font-medium">Fuseau horaire</label>
                <input wire:model.defer="timezone" class="input">
                @error('timezone') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Format date</label>
                <input wire:model.defer="date_format" class="input">
            </div>
            <div>
                <label class="block text-sm font-medium">Préfixe achats</label>
                <input wire:model.defer="purchase_prefix" class="input">
            </div>
            <div>
                <label class="block text-sm font-medium">Préfixe ventes</label>
                <input wire:model.defer="sale_prefix" class="input">
            </div>
            <div>
                <label class="block text-sm font-medium">Taux de taxe (%)</label>
                <input wire:model.defer="tax_rate" type="number" step="0.01" class="input">
            </div>
            <div>
                <label class="block text-sm font-medium">Seuil stock bas global</label>
                <input wire:model.defer="low_stock_threshold" type="number" step="0.001" class="input">
            </div>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-5">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Sécurité</div>
            <div class="mt-1 text-lg font-semibold text-slate-900">Alertes de connexion</div>
            <div class="mt-2 text-sm text-slate-500">Envoie un email à chaque connexion réussie vers une seule adresse de réception configurable.</div>
        </div>
        <div class="grid gap-6 md:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <label class="flex items-start gap-3">
                    <input wire:model.defer="login_alert_enabled" type="checkbox" class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <span>
                        <span class="block text-sm font-medium text-slate-900">Activer les alertes email de connexion</span>
                        <span class="mt-1 block text-sm text-slate-500">Chaque authentification réussie déclenchera un email avec la date, l’IP et le navigateur.</span>
                    </span>
                </label>
            </div>
            <div>
                <label class="block text-sm font-medium">Email destinataire des alertes</label>
                <input wire:model.defer="login_alert_recipient" type="email" class="input" placeholder="alert@entreprise.com">
                @error('login_alert_recipient') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                <div class="mt-2 text-xs text-slate-500">Cette adresse reçoit les alertes. L’email principal de l’entreprise est aussi ajouté automatiquement s’il est renseigné.</div>
            </div>
        </div>
    </div>

    <div class="text-sm text-emerald-600" wire:loading.remove wire:target="save" x-data @saved.window="setTimeout(() => $el.classList.add('hidden'), 2000)">
        Paramètres enregistrés.
    </div>
</div>
