<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            ['name' => 'Fournisseur Local A', 'type' => 'local', 'phone' => '0900000001'],
            ['name' => 'Fournisseur Étranger B', 'type' => 'foreign', 'phone' => '0900000002'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::firstOrCreate(['name' => $supplier['name']], $supplier);
        }

        $customers = [
            ['name' => 'Client Comptant', 'phone' => '0910000001'],
            ['name' => 'Client Crédit', 'phone' => '0910000002'],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(['name' => $customer['name']], $customer);
        }

        $pcs = Unit::where('code', 'pcs')->first();
        $kg = Unit::where('code', 'kg')->first();
        $m = Unit::where('code', 'm')->first();

        $products = [
            ['sku' => 'ART-001', 'name' => 'Marteau', 'unit_id' => $pcs?->id, 'sale_margin_percent' => 20],
            ['sku' => 'ART-002', 'name' => 'Clous 1kg', 'unit_id' => $kg?->id, 'sale_margin_percent' => 15],
            ['sku' => 'ART-003', 'name' => 'Câble 1m', 'unit_id' => $m?->id, 'sale_margin_percent' => 25],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(['sku' => $product['sku']], $product);
        }
    }
}
