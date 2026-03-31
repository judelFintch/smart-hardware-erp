<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-amber-50 to-cyan-50 p-6 shadow-sm">
        <div class="max-w-3xl">
            <div class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-amber-700">
                Sauvegarde
            </div>
            <h1 class="mt-3 text-3xl font-semibold text-slate-900">Snapshot applicatif exportable</h1>
            <p class="mt-2 text-sm text-slate-500">Exporte un instantané JSON des données critiques pour audit, sauvegarde rapide ou support technique. Ce n’est pas un dump SQL complet, mais une capture métier exploitable.</p>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Utilisateurs</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($stats['users']) }}</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Articles</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($stats['products']) }}</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Ventes</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($stats['sales']) }}</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Achats</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($stats['purchases']) }}</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Notifications</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($stats['notifications']) }}</div>
        </div>
    </div>

    <div class="grid gap-4 xl:grid-cols-[1.3fr_0.7fr]">
        <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Contenu du snapshot</h2>
            <div class="mt-4 grid gap-3 text-sm text-slate-600 md:grid-cols-2">
                <div class="rounded-2xl bg-slate-50 p-4">Configuration entreprise et paramètres globaux</div>
                <div class="rounded-2xl bg-slate-50 p-4">Utilisateurs, clients, fournisseurs et emplacements</div>
                <div class="rounded-2xl bg-slate-50 p-4">Catalogue articles, unités et états de stock</div>
                <div class="rounded-2xl bg-slate-50 p-4">Achats, ventes, dépenses et derniers journaux d’activité</div>
            </div>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-slate-950 p-6 text-white shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-300">Action</div>
            <h2 class="mt-3 text-2xl font-semibold">Télécharger la sauvegarde</h2>
            <p class="mt-2 text-sm text-slate-300">Génère un fichier `json` horodaté avec les données principales, y compris les éléments supprimés logiquement.</p>
            <button class="btn mt-6 border-0 bg-cyan-500 text-slate-950 hover:bg-cyan-400" type="button" wire:click="downloadSnapshot">
                Télécharger le snapshot
            </button>
        </div>
    </div>
</div>
