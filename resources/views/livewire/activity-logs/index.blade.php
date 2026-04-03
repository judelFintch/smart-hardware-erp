<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-cyan-50 p-6 shadow-sm">
        <div class="max-w-2xl">
            <div class="inline-flex items-center rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">
                Audit applicatif
            </div>
            <h1 class="mt-3 text-3xl font-semibold text-slate-900">Journal d’activité et traçabilité des actions</h1>
            <p class="mt-2 text-sm text-slate-500">Consulte l’historique des opérations importantes pour comprendre qui a fait quoi et sur quelle entité.</p>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Logs affichés</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($logs->count()) }}</div>
            <div class="mt-2 text-sm text-slate-500">Éléments visibles sur la page actuelle.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Total logs</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($logs->total()) }}</div>
            <div class="mt-2 text-sm text-slate-500">Historique complet actuellement disponible.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Utilisateurs visibles</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($logs->getCollection()->pluck('user.name')->filter()->unique()->count()) }}</div>
            <div class="mt-2 text-sm text-slate-500">Auteurs distincts présents sur cette page.</div>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        @if ($logs->isEmpty())
            <x-empty-state
                title="Aucune activité"
                description="Les actions seront listées ici."
            />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/80">
                        <tr class="text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Utilisateur</th>
                            <th class="px-4 py-3">Action</th>
                            <th class="px-4 py-3">Entité</th>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Détails</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($logs as $log)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-4 text-sm text-slate-600">{{ $log->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-4">
                                    <div class="font-medium text-slate-900">{{ $log->user?->name ?? 'Système' }}</div>
                                    <div class="mt-1 text-xs text-slate-500">Origine de l'action tracée.</div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex rounded-full bg-cyan-100 px-2.5 py-1 text-xs font-medium text-cyan-700">{{ $log->action }}</span>
                                </td>
                                <td class="px-4 py-4 text-sm text-slate-600">{{ class_basename($log->subject_type) }}</td>
                                <td class="px-4 py-4 font-medium text-slate-900">{{ $log->subject_id }}</td>
                                <td class="px-4 py-4 text-sm text-slate-600">
                                    <div class="font-medium text-slate-900">{{ $log->description ?: 'Sans description' }}</div>
                                    @if (($log->meta['source'] ?? null) === 'import')
                                        <div class="mt-1 text-xs text-slate-500">
                                            Fichier: {{ $log->meta['file_name'] ?? '—' }}
                                            · Crees: {{ $log->meta['summary']['created'] ?? 0 }}
                                            · Mises a jour: {{ $log->meta['summary']['updated'] ?? 0 }}
                                            · Ignorees: {{ $log->meta['summary']['skipped'] ?? 0 }}
                                        </div>
                                    @endif
                                    @if (!empty($log->meta['changes']))
                                        <div class="mt-1 space-y-1 text-xs text-slate-500">
                                            @foreach (collect($log->meta['changes'])->take(4) as $field => $change)
                                                <div>
                                                    <span class="font-medium text-slate-700">{{ \Illuminate\Support\Str::headline($field) }}:</span>
                                                    {{ $change['old'] ?? '∅' }} → {{ $change['new'] ?? '∅' }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    <div>
        {{ $logs->links() }}
    </div>
</div>
