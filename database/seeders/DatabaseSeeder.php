<?php

namespace Database\Seeders;

use App\Models\StockLocation;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        StockLocation::firstOrCreate(
            ['code' => 'depot'],
            ['name' => 'Dépôt']
        );

        StockLocation::firstOrCreate(
            ['code' => 'magasin'],
            ['name' => 'Magasin']
        );

        $this->call([
            AdminUserSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}
