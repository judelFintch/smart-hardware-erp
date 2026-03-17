<div class="space-y-4">
    <div>
        <h1 class="text-2xl font-semibold">Santé système</h1>
        <p class="text-sm text-slate-500">Statut des services et configuration.</p>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div class="card p-5 space-y-2">
            <div class="text-sm text-slate-500">Base de données</div>
            <div class="flex items-center gap-2">
                <span class="badge {{ $data['db']['ok'] ? 'badge-success' : 'badge-danger' }}">
                    {{ $data['db']['ok'] ? 'OK' : 'Erreur' }}
                </span>
                @if (!$data['db']['ok'])
                    <span class="text-xs text-red-600">{{ $data['db']['error'] }}</span>
                @endif
            </div>
        </div>

        <div class="card p-5 space-y-2">
            <div class="text-sm text-slate-500">Cache</div>
            <div class="flex items-center gap-2">
                <span class="badge {{ $data['cache']['ok'] ? 'badge-success' : 'badge-danger' }}">
                    {{ $data['cache']['ok'] ? 'OK' : 'Erreur' }}
                </span>
                @if (!$data['cache']['ok'])
                    <span class="text-xs text-red-600">{{ $data['cache']['error'] }}</span>
                @endif
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="card p-5 space-y-2">
            <div class="text-sm text-slate-500">Stockage</div>
            <span class="badge {{ $data['storage']['ok'] ? 'badge-success' : 'badge-danger' }}">
                {{ $data['storage']['ok'] ? 'Writable' : 'Non writable' }}
            </span>
        </div>
        <div class="card p-5 space-y-2">
            <div class="text-sm text-slate-500">Dossier public</div>
            <span class="badge {{ $data['public']['ok'] ? 'badge-success' : 'badge-danger' }}">
                {{ $data['public']['ok'] ? 'Writable' : 'Non writable' }}
            </span>
        </div>
        <div class="card p-5 space-y-2">
            <div class="text-sm text-slate-500">Queue</div>
            <span class="badge badge-info">{{ $data['queue']['driver'] }}</span>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div class="card p-5 space-y-2">
            <div class="text-sm text-slate-500">Mail</div>
            <div class="flex items-center gap-2">
                <span class="badge badge-info">{{ $data['mail']['driver'] }}</span>
                <span class="text-xs text-slate-500">{{ $data['mail']['host'] }}</span>
            </div>
        </div>
        <div class="card p-5 space-y-2">
            <div class="text-sm text-slate-500">Application</div>
            <div class="text-sm">Env: <span class="font-medium">{{ $data['app']['env'] }}</span></div>
            <div class="text-sm">Debug: <span class="font-medium">{{ $data['app']['debug'] }}</span></div>
            <div class="text-sm">Timezone: <span class="font-medium">{{ $data['app']['timezone'] }}</span></div>
        </div>
    </div>
</div>
