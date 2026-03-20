<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Form extends Component
{
    public ?User $user = null;
    public string $name = '';
    public string $email = '';
    public string $role = 'seller';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(?User $user = null): void
    {
        if ($user && $user->exists) {
            $this->user = $user;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role = $user->role;
        }
    }

    public function save(): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user?->id)->whereNull('deleted_at')],
            'role' => ['required', 'in:owner,manager,seller'],
        ];

        if ($this->user) {
            if ($this->password !== '') {
                $rules['password'] = ['confirmed', 'min:12'];
            }
        } else {
            $rules['password'] = ['required', 'confirmed', 'min:12'];
        }

        $data = $this->validate($rules);

        if ($this->user) {
            $this->user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => $data['role'],
                'password' => $data['password'] ? Hash::make($data['password']) : $this->user->password,
            ]);
        } else {
            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => $data['role'],
                'password' => Hash::make($data['password']),
                'email_verified_at' => now(),
            ]);
        }

        $this->redirectRoute('users.index');
    }

    public function render()
    {
        $title = $this->user ? 'Modifier utilisateur' : 'Nouvel utilisateur';

        return view('livewire.users.form', compact('title'))
            ->layout('layouts.app');
    }
}
