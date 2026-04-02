<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-cyan-50 p-6 shadow-sm">
        <div class="max-w-3xl">
            <div class="inline-flex items-center rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">
                Santé système
            </div>
            <h1 class="mt-3 text-3xl font-semibold text-slate-900">Services, stockage et configuration applicative</h1>
            <p class="mt-2 text-sm text-slate-500">Diagnostic rapide de l’infrastructure applicative: connectivité, drivers actifs, journaux, espace disque et version runtime.</p>
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
            <div class="mt-1 text-xs text-slate-400">Libre: {{ $data['storage']['free_space'] }}</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Queue</div>
            <div class="mt-3 text-2xl font-semibold text-slate-900">{{ $data['queue']['driver'] }}</div>
            <div class="mt-2 text-sm text-slate-500">Driver utilisé pour les traitements asynchrones.</div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
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
            <div class="mt-2 text-sm text-slate-600">Alertes connexion: <span class="font-medium text-slate-900">{{ $data['mail']['login_alert_enabled'] ? 'Actives' : 'Inactives' }}</span></div>
            <div class="mt-2 text-sm text-slate-600">Destinataire: <span class="font-medium text-slate-900">{{ $data['mail']['login_alert_recipient'] ?: 'Non configuré' }}</span></div>
            <div class="mt-4">
                @php
                    $mailStatusTone = match ($data['mail']['login_alert_last_status']) {
                        'success' => 'bg-emerald-100 text-emerald-700',
                        'failed' => 'bg-red-100 text-red-700',
                        default => 'bg-slate-100 text-slate-700',
                    };
                    $mailStatusLabel = match ($data['mail']['login_alert_last_status']) {
                        'success' => 'Dernier envoi réussi',
                        'failed' => 'Dernier envoi échoué',
                        default => 'Aucun envoi tracé',
                    };
                @endphp
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $mailStatusTone }}">
                    {{ $mailStatusLabel }}
                </span>
            </div>
            @if ($data['mail']['login_alert_last_attempt_at'])
                <div class="mt-2 text-sm text-slate-600">Dernière tentative: <span class="font-medium text-slate-900">{{ $data['mail']['login_alert_last_attempt_at'] }}</span></div>
            @endif
            @if ($data['mail']['login_alert_last_error'])
                <div class="mt-3 rounded-2xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                    <div class="font-semibold">Cause du dernier échec</div>
                    <div class="mt-1 break-words">{{ $data['mail']['login_alert_last_error'] }}</div>
                </div>
            @endif
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Session</h2>
            <div class="mt-4 text-sm text-slate-600">Driver: <span class="font-medium text-slate-900">{{ $data['session']['driver'] }}</span></div>
            <div class="mt-2 text-sm text-slate-500">Support d’authentification et persistance des sessions.</div>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Application</h2>
            <div class="mt-4 space-y-2 text-sm text-slate-600">
                <div>Env: <span class="font-medium text-slate-900">{{ $data['app']['env'] }}</span></div>
                <div>Debug: <span class="font-medium text-slate-900">{{ $data['app']['debug'] }}</span></div>
                <div>Timezone: <span class="font-medium text-slate-900">{{ $data['app']['timezone'] }}</span></div>
                <div>PHP: <span class="font-medium text-slate-900">{{ $data['app']['php'] }}</span></div>
                <div>Laravel: <span class="font-medium text-slate-900">{{ $data['app']['laravel'] }}</span></div>
            </div>
        </div>
    </div>

    <div class="grid gap-4 xl:grid-cols-3">
        <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Base de données</h2>
            <div class="mt-4 space-y-2 text-sm text-slate-600">
                <div>Connexion: <span class="font-medium text-slate-900">{{ $data['db']['connection'] }}</span></div>
                <div>Base active: <span class="font-medium text-slate-900">{{ $data['db']['database'] }}</span></div>
            </div>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Cache et URL</h2>
            <div class="mt-4 space-y-2 text-sm text-slate-600">
                <div>Cache: <span class="font-medium text-slate-900">{{ $data['cache']['driver'] }}</span></div>
                <div>URL app: <span class="font-medium break-all text-slate-900">{{ $data['app']['url'] }}</span></div>
            </div>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Journal applicatif</h2>
            <div class="mt-4 space-y-2 text-sm text-slate-600">
                <div>Écriture: <span class="font-medium text-slate-900">{{ $data['logs']['writable'] ? 'OK' : 'Erreur' }}</span></div>
                <div>Dernier fichier: <span class="font-medium text-slate-900">{{ $data['logs']['latest_file'] ?? 'Aucun' }}</span></div>
                <div>Taille: <span class="font-medium text-slate-900">{{ $data['logs']['latest_size'] }}</span></div>
            </div>
        </div>
    </div>
</div>
