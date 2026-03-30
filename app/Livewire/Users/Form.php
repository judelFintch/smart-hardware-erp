<?php

namespace App\Livewire\Users;

use App\Models\StockLocation;
use App\Models\User;
use App\Support\LocationAccess;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Form extends Component
{
    public ?User $user = null;
    public string $name = '';
    public string $email = '';
    public string $role = 'seller';
    public ?int $stock_location_id = null;
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(?User $user = null): void
    {
        if ($user && $user->exists && !LocationAccess::hasGlobalAccess()) {
            abort_unless(
                (int) $user->stock_location_id === (int) LocationAccess::assignedLocationId(),
                403,
                'Acces non autorise a cet utilisateur.'
            );
        }

        if ($user && $user->exists) {
            $this->user = $user;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role = $user->role;
            $this->stock_location_id = $user->stock_location_id;
        }

        if (!LocationAccess::hasGlobalAccess() && !$this->stock_location_id) {
            $this->stock_location_id = LocationAccess::assignedLocationId();
        }
    }

    public function save(): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user?->id)->whereNull('deleted_at')],
            'role' => [LocationAccess::hasGlobalAccess() ? 'required' : 'required', LocationAccess::hasGlobalAccess() ? 'in:owner,manager,seller' : 'in:manager,seller'],
            'stock_location_id' => [$this->role === 'owner' ? 'nullable' : 'required', 'exists:stock_locations,id'],
        ];

        if ($this->user) {
            if ($this->password !== '') {
                $rules['password'] = ['confirmed', 'min:12'];
            }
        } else {
            $rules['password'] = ['required', 'confirmed', 'min:12'];
        }

        $data = $this->validate($rules);
        if (!LocationAccess::hasGlobalAccess()) {
            $data['stock_location_id'] = LocationAccess::assignedLocationId();
        }

        if (($data['role'] ?? $this->role) === 'owner') {
            $data['stock_location_id'] = null;
        }

        if ($this->user) {
            $this->user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => $data['role'],
                'stock_location_id' => $data['stock_location_id'],
                'password' => $data['password'] ? Hash::make($data['password']) : $this->user->password,
            ]);
        } else {
            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => $data['role'],
                'stock_location_id' => $data['stock_location_id'],
                'password' => Hash::make($data['password']),
                'email_verified_at' => now(),
            ]);
        }

        $this->redirectRoute('users.index');
    }

    public function render()
    {
        $title = $this->user ? 'Modifier utilisateur' : 'Nouvel utilisateur';
        $locations = LocationAccess::restrictLocations(StockLocation::query()->orderBy('name'))->get();
        $canSelectAnyLocation = LocationAccess::hasGlobalAccess();

        return view('livewire.users.form', compact('title', 'locations', 'canSelectAnyLocation'))
            ->layout('layouts.app');
    }
}
