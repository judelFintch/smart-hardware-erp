<?php

namespace App\Livewire\Users;

use App\Livewire\Concerns\ConfirmsDeletionWithSecretCode;
use App\Models\User;
use App\Support\LocationAccess;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use ConfirmsDeletionWithSecretCode, WithPagination;

    public int $perPage = 15;

    protected function performDelete(int $userId): void
    {
        if (auth()->id() === $userId) {
            $this->addError('delete', 'Impossible de supprimer votre propre compte.');
            return;
        }

        User::whereKey($userId)->delete();
    }

    public function render()
    {
        $users = User::with('stockLocation')
            ->when(!LocationAccess::hasGlobalAccess(), fn ($query) => $query->where('stock_location_id', LocationAccess::assignedLocationId()))
            ->orderBy('name')
            ->paginate($this->perPage);

        return view('livewire.users.index', compact('users'))
            ->layout('layouts.app');
    }
}
