<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;

class Index extends Component
{
    public function delete(int $userId): void
    {
        if (auth()->id() === $userId) {
            $this->addError('delete', 'Impossible de supprimer votre propre compte.');
            return;
        }

        User::whereKey($userId)->delete();
    }

    public function render()
    {
        $users = User::orderBy('name')->get();

        return view('livewire.users.index', compact('users'))
            ->layout('layouts.app');
    }
}
