<?php

namespace Database\Seeders;

use App\Models\StockLocation;
use Illuminate\Database\Seeder;

class StockLocationsSeeder extends Seeder
{
    public function run(): void
    {
        StockLocation::firstOrCreate(
            ['code' => 'depot'],
            ['name' => 'Dépôt']
        );

        StockLocation::firstOrCreate(
            ['code' => 'magasin'],
            ['name' => 'Magasin', 'is_default_sale' => true]
        );

        StockLocation::firstOrCreate(
            ['code' => 'commande'],
            ['name' => 'Commande']
        );

        if (!StockLocation::query()->where('is_default_sale', true)->exists()) {
            StockLocation::query()->where('code', 'magasin')->update(['is_default_sale' => true]);
        }
    }
}
