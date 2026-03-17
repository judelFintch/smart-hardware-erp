<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold">Journal d’activité</h1>
            <p class="text-sm text-slate-500">Traçabilité des actions clés.</p>
        </div>
    </div>

    <div class="card overflow-hidden">
        @if ($logs->isEmpty())
            <x-empty-state
                title="Aucune activité"
                description="Les actions seront listées ici."
            />
        @else
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b">
                        <th class="p-3">Date</th>
                        <th class="p-3">Utilisateur</th>
                        <th class="p-3">Action</th>
                        <th class="p-3">Entité</th>
                        <th class="p-3">ID</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr class="border-b">
                            <td class="p-3 text-sm text-slate-500">{{ $log->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="p-3">{{ $log->user?->name ?? 'Système' }}</td>
                            <td class="p-3">
                                <span class="badge badge-info">{{ $log->action }}</span>
                            </td>
                            <td class="p-3">{{ class_basename($log->subject_type) }}</td>
                            <td class="p-3">{{ $log->subject_id }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    <div>
        {{ $logs->links() }}
    </div>
</div>
