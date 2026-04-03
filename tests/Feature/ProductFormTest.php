<?php

namespace Tests\Feature;

use App\Livewire\Products\Form;
use App\Livewire\Products\Index;
use App\Models\Product;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class ProductFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_created_when_margin_is_left_blank(): void
    {
        $user = User::factory()->create([
            'role' => 'owner',
        ]);

        $unit = Unit::create([
            'code' => 'pcs',
            'name' => 'Pieces',
            'type' => 'piece',
        ]);

        $this->actingAs($user);

        Livewire::test(Form::class)
            ->set('sku', 'SKU-011')
            ->set('name', 'AIL BOX')
            ->set('unit_id', $unit->id)
            ->set('sale_price_local', 15000)
            ->set('sale_margin_percent', '')
            ->set('reorder_level', 5)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('products.index', absolute: false));

        $this->assertDatabaseHas('products', [
            'sku' => 'SKU-011',
            'name' => 'AIL BOX',
            'unit_id' => $unit->id,
            'sale_price_local' => 15000,
            'sale_margin_percent' => 0,
            'reorder_level' => 5,
        ]);

        $product = Product::where('sku', 'SKU-011')->firstOrFail();

        $this->assertSame(0.0, (float) $product->sale_margin_percent);
    }

    public function test_product_import_can_create_product_when_margin_is_blank(): void
    {
        $user = User::factory()->create([
            'role' => 'owner',
        ]);

        Unit::create([
            'code' => 'pcs',
            'name' => 'Pieces',
            'type' => 'piece',
        ]);

        $file = UploadedFile::fake()->createWithContent(
            'products.csv',
            "sku,name,barcode,unit_code,description,cost,price,stock,margin,reorder_level\nSKU-012,AIL BOX,,pcs,,9,15000,, ,5\n"
        );

        $this->actingAs($user);

        Livewire::test(Index::class)
            ->set('importFile', $file)
            ->set('import_location_id', null)
            ->call('importCsv')
            ->assertSet('importSummary.processed', 1)
            ->assertSet('importSummary.created', 1)
            ->assertSet('importSummary.updated', 0)
            ->assertSet('importSummary.skipped', 0)
            ->assertSet('importSummary.skipped_duplicate_sku', 0)
            ->assertSet('importSummary.skipped_barcode_conflict', 0)
            ->assertSet('importSummary.skipped_invalid', 0)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('products', [
            'sku' => 'SKU-012',
            'name' => 'AIL BOX',
            'avg_cost_local' => 9,
            'sale_price_local' => 15000,
            'sale_margin_percent' => 0,
            'reorder_level' => 5,
        ]);
    }

    public function test_product_import_skips_duplicate_sku_rows_in_same_file(): void
    {
        $user = User::factory()->create([
            'role' => 'owner',
        ]);

        Unit::create([
            'code' => 'pcs',
            'name' => 'Pieces',
            'type' => 'piece',
        ]);

        $file = UploadedFile::fake()->createWithContent(
            'products.csv',
            "sku,name,barcode,unit_code,description,cost,price,stock,margin,reorder_level\nSKU-013,Produit A,,pcs,,10,100,,5,2\nSKU-013,Produit B,,pcs,,10,150,,8,3\n"
        );

        $this->actingAs($user);

        Livewire::test(Index::class)
            ->set('importFile', $file)
            ->set('import_location_id', null)
            ->call('importCsv')
            ->assertSet('importSummary.processed', 1)
            ->assertSet('importSummary.created', 1)
            ->assertSet('importSummary.updated', 0)
            ->assertSet('importSummary.skipped', 1)
            ->assertSet('importSummary.skipped_duplicate_sku', 1)
            ->assertSet('importSummary.skipped_barcode_conflict', 0)
            ->assertSet('importSummary.skipped_invalid', 0)
            ->assertHasNoErrors();

        $this->assertDatabaseCount('products', 1);
        $this->assertDatabaseHas('products', [
            'sku' => 'SKU-013',
            'name' => 'Produit A',
            'sale_price_local' => 100,
        ]);
    }

    public function test_product_import_skips_barcode_conflicts(): void
    {
        $user = User::factory()->create([
            'role' => 'owner',
        ]);

        $unit = Unit::create([
            'code' => 'pcs',
            'name' => 'Pieces',
            'type' => 'piece',
        ]);

        Product::create([
            'sku' => 'SKU-014',
            'name' => 'Produit existant',
            'barcode' => 'BAR-001',
            'unit_id' => $unit->id,
            'sale_margin_percent' => 0,
            'reorder_level' => 0,
            'is_active' => true,
        ]);

        $file = UploadedFile::fake()->createWithContent(
            'products.csv',
            "sku,name,barcode,unit_code,description,cost,price,stock,margin,reorder_level\nSKU-015,Nouveau produit,BAR-001,pcs,,10,100,,5,2\n"
        );

        $this->actingAs($user);

        Livewire::test(Index::class)
            ->set('importFile', $file)
            ->set('import_location_id', null)
            ->call('importCsv')
            ->assertSet('importSummary.processed', 0)
            ->assertSet('importSummary.created', 0)
            ->assertSet('importSummary.updated', 0)
            ->assertSet('importSummary.skipped', 1)
            ->assertSet('importSummary.skipped_duplicate_sku', 0)
            ->assertSet('importSummary.skipped_barcode_conflict', 1)
            ->assertSet('importSummary.skipped_invalid', 0)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('products', [
            'sku' => 'SKU-015',
        ]);
        $this->assertDatabaseCount('products', 1);
    }
}
