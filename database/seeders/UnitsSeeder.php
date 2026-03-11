<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitsSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['code' => 'pcs', 'name' => 'Pièce', 'type' => 'piece'],
            ['code' => 'kg', 'name' => 'Kilogramme', 'type' => 'weight'],
            ['code' => 'g', 'name' => 'Gramme', 'type' => 'weight'],
            ['code' => 'l', 'name' => 'Litre', 'type' => 'volume'],
            ['code' => 'm', 'name' => 'Mètre', 'type' => 'other'],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(['code' => $unit['code']], $unit);
        }
    }
}
