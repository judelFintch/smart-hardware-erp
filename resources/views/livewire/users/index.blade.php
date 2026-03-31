<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-cyan-50 p-6 shadow-sm">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-2xl">
                <div class="inline-flex items-center rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">
                    Comptes utilisateurs
                </div>
                <h1 class="mt-3 text-3xl font-semibold text-slate-900">Équipe, accès et rôles applicatifs</h1>
                <p class="mt-2 text-sm text-slate-500">Gère les comptes de l'organisation et surveille rapidement la répartition des accès.</p>
            </div>
            <a href="{{ route('users.create') }}" class="btn btn-primary" wire:navigate>Nouvel utilisateur</a>
        </div>
    </div>

    @error('delete') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Utilisateurs affichés</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($users->count()) }}</div>
            <div class="mt-2 text-sm text-slate-500">Comptes visibles sur la page en cours.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Total utilisateurs</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($users->total()) }}</div>
            <div class="mt-2 text-sm text-slate-500">Tous les comptes actifs dans l'application.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Rôles affichés</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($users->getCollection()->pluck('role')->filter()->unique()->count()) }}</div>
            <div class="mt-2 text-sm text-slate-500">Variété des profils visibles sur cette page.</div>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        @if ($users->isEmpty())
            <x-empty-state
                title="Aucun utilisateur"
                description="Ajoutez des comptes pour votre équipe."
                action="Nouvel utilisateur"
                :action-href="route('users.create')"
            />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/80">
                        <tr class="text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                            <th class="px-4 py-3">Utilisateur</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Rôle</th>
                            <th class="px-4 py-3">Affectation</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($users as $user)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-4">
                                    <div class="font-medium text-slate-900">{{ $user->name }}</div>
                                    <div class="mt-1 text-xs text-slate-500">{{ auth()->id() === $user->id ? 'Compte actuellement connecté.' : 'Compte membre de l’équipe.' }}</div>
                                </td>
                                <td class="px-4 py-4 text-sm text-slate-600">{{ $user->email }}</td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex rounded-full bg-cyan-100 px-2.5 py-1 text-xs font-medium text-cyan-700">{{ $user->role }}</span>
                                </td>
                                <td class="px-4 py-4 text-sm text-slate-600">
                                    {{ $user->stockLocation?->name ?? ($user->role === 'owner' ? 'Tous les emplacements' : 'Non affecté') }}
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-secondary" wire:navigate>Modifier</a>
                                        <button wire:click="delete({{ $user->id }})" class="btn btn-secondary text-red-600" type="button" data-confirm="Confirmer la suppression de cet utilisateur ? Il restera restaurable depuis la corbeille.">Supprimer</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    <div>
        {{ $users->links() }}
    </div>
</div>
