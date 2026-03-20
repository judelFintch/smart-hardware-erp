<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-cyan-50 p-6 shadow-sm">
        <div class="max-w-2xl">
            <div class="inline-flex items-center rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">
                Santé système
            </div>
            <h1 class="mt-3 text-3xl font-semibold text-slate-900">Services, stockage et configuration applicative</h1>
            <p class="mt-2 text-sm text-slate-500">Vue rapide de l’état technique de l’environnement pour repérer les points de défaillance ou de configuration.</p>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Base de données</div>
            <div class="mt-3">
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $data['db']['ok'] ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                    {{ $data['db']['ok'] ? 'OK' : 'Erreur' }}
                </span>
            </div>
            @if (!$data['db']['ok'])
                <div class="mt-2 text-xs text-red-600">{{ $data['db']['error'] }}</div>
            @endif
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Cache</div>
            <div class="mt-3">
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $data['cache']['ok'] ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                    {{ $data['cache']['ok'] ? 'OK' : 'Erreur' }}
                </span>
            </div>
            @if (!$data['cache']['ok'])
                <div class="mt-2 text-xs text-red-600">{{ $data['cache']['error'] }}</div>
            @endif
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Stockage</div>
            <div class="mt-3">
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $data['storage']['ok'] ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                    {{ $data['storage']['ok'] ? 'Writable' : 'Non writable' }}
                </span>
            </div>
            <div class="mt-2 text-sm text-slate-500">Écriture des fichiers applicatifs.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Queue</div>
            <div class="mt-3 text-2xl font-semibold text-slate-900">{{ $data['queue']['driver'] }}</div>
            <div class="mt-2 text-sm text-slate-500">Driver utilisé pour les traitements asynchrones.</div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Répertoire public</h2>
            <div class="mt-4">
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $data['public']['ok'] ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                    {{ $data['public']['ok'] ? 'Writable' : 'Non writable' }}
                </span>
            </div>
            <div class="mt-2 text-sm text-slate-500">Capacité d’écriture pour les assets et liens publics.</div>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Mail</h2>
            <div class="mt-4 text-sm text-slate-600">Driver: <span class="font-medium text-slate-900">{{ $data['mail']['driver'] }}</span></div>
            <div class="mt-2 text-sm text-slate-600">Host: <span class="font-medium text-slate-900">{{ $data['mail']['host'] }}</span></div>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Application</h2>
            <div class="mt-4 space-y-2 text-sm text-slate-600">
                <div>Env: <span class="font-medium text-slate-900">{{ $data['app']['env'] }}</span></div>
                <div>Debug: <span class="font-medium text-slate-900">{{ $data['app']['debug'] }}</span></div>
                <div>Timezone: <span class="font-medium text-slate-900">{{ $data['app']['timezone'] }}</span></div>
            </div>
        </div>
    </div>
</div>
