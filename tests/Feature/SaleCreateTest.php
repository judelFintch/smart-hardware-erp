<?php

namespace Tests\Feature;

use App\Livewire\Sales\Create;
use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SaleCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_quick_add_adds_product_to_first_empty_sale_line(): void
    {
        $user = User::factory()->create(['role' => 'owner']);
        $location = StockLocation::query()->create(['code' => 'MAG', 'name' => 'Magasin']);
        $product = Product::query()->create([
            'sku' => 'SKU-SALE-001',
            'name' => 'Marteau',
            'sale_price_local' => 10,
            'is_active' => true,
        ]);

        StockBalance::query()->create([
            'product_id' => $product->id,
            'location_id' => $location->id,
            'quantity' => 5,
            'sale_price_local' => 10,
        ]);

        $this->actingAs($user);

        Livewire::test(Create::class)
            ->set('location_id', $location->id)
            ->call('addProduct', $product->id)
            ->assertSet('items.0.product_id', $product->id)
            ->assertSet('items.0.quantity', '1.000');
    }

    public function test_quick_add_merges_duplicate_product_by_incrementing_quantity(): void
    {
        $user = User::factory()->create(['role' => 'owner']);
        $location = StockLocation::query()->create(['code' => 'MAG2', 'name' => 'Magasin 2']);
        $product = Product::query()->create([
            'sku' => 'SKU-SALE-002',
            'name' => 'Tournevis',
            'sale_price_local' => 12,
            'is_active' => true,
        ]);

        StockBalance::query()->create([
            'product_id' => $product->id,
            'location_id' => $location->id,
            'quantity' => 5,
            'sale_price_local' => 12,
        ]);

        $this->actingAs($user);

        Livewire::test(Create::class)
            ->set('location_id', $location->id)
            ->call('addProduct', $product->id)
            ->call('addProduct', $product->id)
            ->assertSet('items.0.product_id', $product->id)
            ->assertSet('items.0.quantity', '2.000');
    }
}
