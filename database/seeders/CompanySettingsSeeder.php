<?php

namespace Database\Seeders;

use App\Models\CompanySetting;
use Illuminate\Database\Seeder;

class CompanySettingsSeeder extends Seeder
{
    public function run(): void
    {
        CompanySetting::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Quincaillerie',
                'currency' => 'CDF',
            ]
        );
    }
}
