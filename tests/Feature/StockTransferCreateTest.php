<?php

namespace Tests\Feature;

use App\Livewire\StockTransfers\Create;
use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockLocation;
use App\Models\StockTransfer;
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
            ->call('goToConfirmation')
            ->assertHasErrors('items');
    }

    public function test_max_button_fills_available_quantity_for_selected_product(): void
    {
        $user = User::factory()->create(['role' => 'owner']);

        $source = StockLocation::query()->create(['code' => 'SRC3', 'name' => 'Source 3']);
        $product = Product::query()->create([
            'sku' => 'SKU-TR-004',
            'name' => 'Prise simple',
            'is_active' => true,
        ]);

        StockBalance::query()->create([
            'product_id' => $product->id,
            'location_id' => $source->id,
            'quantity' => 12.5,
        ]);

        $this->actingAs($user);

        Livewire::test(Create::class)
            ->set('from_location_id', $source->id)
            ->set('items.0.product_id', $product->id)
            ->call('fillMaxQuantity', 0)
            ->assertSet('items.0.quantity', '12.500');
    }

    public function test_same_product_can_not_be_selected_twice(): void
    {
        $user = User::factory()->create(['role' => 'owner']);

        $source = StockLocation::query()->create(['code' => 'SRC4', 'name' => 'Source 4']);
        $product = Product::query()->create([
            'sku' => 'SKU-TR-005',
            'name' => 'Boite de derivation',
            'is_active' => true,
        ]);

        StockBalance::query()->create([
            'product_id' => $product->id,
            'location_id' => $source->id,
            'quantity' => 8,
        ]);

        $this->actingAs($user);

        Livewire::test(Create::class)
            ->set('from_location_id', $source->id)
            ->set('items.0.product_id', $product->id)
            ->set('items.1.product_id', $product->id)
            ->assertSet('items.1.product_id', null)
            ->assertHasErrors('items');
    }

    public function test_transfer_goes_through_confirmation_before_persistence_and_creates_receipt(): void
    {
        $user = User::factory()->create(['role' => 'owner']);

        $source = StockLocation::query()->create(['code' => 'SRC5', 'name' => 'Source 5']);
        $destination = StockLocation::query()->create(['code' => 'DST5', 'name' => 'Destination 5']);
        $product = Product::query()->create([
            'sku' => 'SKU-TR-006',
            'name' => 'Cable UTP',
            'is_active' => true,
        ]);

        StockBalance::query()->create([
            'product_id' => $product->id,
            'location_id' => $source->id,
            'quantity' => 10,
            'avg_cost_local' => 5,
        ]);

        $this->actingAs($user);

        Livewire::test(Create::class)
            ->set('from_location_id', $source->id)
            ->set('to_location_id', $destination->id)
            ->set('items', [
                ['product_id' => $product->id, 'quantity' => 4],
            ])
            ->call('goToConfirmation')
            ->assertSet('step', 2);

        $this->assertDatabaseCount('stock_transfers', 0);

        Livewire::test(Create::class)
            ->set('from_location_id', $source->id)
            ->set('to_location_id', $destination->id)
            ->set('items', [
                ['product_id' => $product->id, 'quantity' => 4],
            ])
            ->call('goToConfirmation')
            ->call('confirmTransfer')
            ->assertSet('step', 3);

        $transfer = StockTransfer::query()->first();

        $this->assertNotNull($transfer);
        $this->assertDatabaseHas('stock_movements', [
            'reference_type' => 'stock_transfer',
            'reference_id' => $transfer->id,
        ]);

        $this->get(route('stock-transfers.print', $transfer))
            ->assertOk()
            ->assertSee($transfer->reference);
    }
}
