<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold">Utilisateurs</h1>
            <p class="text-sm text-slate-500">Gestion des accès et rôles.</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary" wire:navigate>Nouvel utilisateur</a>
    </div>

    @error('delete') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror

    <div class="card overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="text-left border-b">
                    <th class="p-3">Nom</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Rôle</th>
                    <th class="p-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="border-b">
                        <td class="p-3">{{ $user->name }}</td>
                        <td class="p-3">{{ $user->email }}</td>
                        <td class="p-3">
                            <span class="badge badge-info">{{ $user->role }}</span>
                        </td>
                        <td class="p-3">
                            <a href="{{ route('users.edit', $user) }}" class="text-blue-600" wire:navigate>Modifier</a>
                            <button wire:click="delete({{ $user->id }})" class="text-red-600 ml-2" type="button">Supprimer</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
