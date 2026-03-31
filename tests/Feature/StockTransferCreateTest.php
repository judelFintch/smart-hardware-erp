<?php

namespace Tests\Feature;

use App\Livewire\StockTransfers\Create;
use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StockTransferCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_transfer_form_only_lists_products_available_in_source_location(): void
    {
        $user = User::factory()->create(['role' => 'owner']);

        $source = StockLocation::query()->create(['code' => 'SRC', 'name' => 'Source']);
        $destination = StockLocation::query()->create(['code' => 'DST', 'name' => 'Destination']);

        $availableProduct = Product::query()->create([
            'sku' => 'SKU-TR-001',
            'name' => 'Cable cuivre',
            'is_active' => true,
        ]);

        $unavailableProduct = Product::query()->create([
            'sku' => 'SKU-TR-002',
            'name' => 'Interrupteur mural',
            'is_active' => true,
        ]);

        StockBalance::query()->create([
            'product_id' => $availableProduct->id,
            'location_id' => $source->id,
            'quantity' => 5,
        ]);

        StockBalance::query()->create([
            'product_id' => $unavailableProduct->id,
            'location_id' => $destination->id,
            'quantity' => 7,
        ]);

        $this->actingAs($user);

        Livewire::test(Create::class)
            ->set('from_location_id', $source->id)
            ->assertSee('Cable cuivre')
            ->assertDontSee('Interrupteur mural');
    }

    public function test_transfer_form_rejects_manual_submission_of_product_without_source_stock(): void
    {
        $user = User::factory()->create(['role' => 'owner']);

        $source = StockLocation::query()->create(['code' => 'SRC2', 'name' => 'Source 2']);
        $destination = StockLocation::query()->create(['code' => 'DST2', 'name' => 'Destination 2']);

        $product = Product::query()->create([
            'sku' => 'SKU-TR-003',
            'name' => 'Disjoncteur',
            'is_active' => true,
        ]);

        $this->actingAs($user);

        Livewire::test(Create::class)
            ->set('from_location_id', $source->id)
            ->set('to_location_id', $destination->id)
            ->set('items', [
                ['product_id' => $product->id, 'quantity' => 1],
            ])
            ->call('save')
            ->assertHasErrors('items');
    }
}
