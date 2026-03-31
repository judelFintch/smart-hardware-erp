<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPassword = 'Admin@12345';
        $defaultSecretCode = '123456';

        User::updateOrCreate(
            ['email' => 'admin@local.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make($defaultPassword),
                'secret_code' => Hash::make($defaultSecretCode),
                'role' => 'owner',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager@local.test'],
            [
                'name' => 'Manager',
                'password' => Hash::make($defaultPassword),
                'secret_code' => Hash::make($defaultSecretCode),
                'role' => 'manager',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'seller@local.test'],
            [
                'name' => 'Seller',
                'password' => Hash::make($defaultPassword),
                'secret_code' => Hash::make($defaultSecretCode),
                'role' => 'seller',
                'email_verified_at' => now(),
            ]
        );
    }
}
