<?php

namespace Tests\Feature;

use App\Models\AppNotification;
use App\Models\CompanySetting;
use App\Models\Product;
use App\Models\StockLocation;
use App\Models\User;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_low_stock_notification_is_created_and_resolved_with_stock_movements(): void
    {
        User::factory()->create(['role' => 'owner']);
        User::factory()->create(['role' => 'manager']);

        CompanySetting::query()->create([
            'name' => 'Quin Brandy',
            'low_stock_threshold' => 5,
        ]);

        $product = Product::query()->create([
            'sku' => 'SKU-LOW-001',
            'name' => 'Article test',
            'sale_margin_percent' => 0,
            'reorder_level' => 0,
            'is_active' => true,
        ]);

        $location = StockLocation::query()->create([
            'code' => 'MAG-001',
            'name' => 'Magasin central',
        ]);

        $service = app(StockService::class);

        $service->recordMovement([
            'product_id' => $product->id,
            'to_location_id' => $location->id,
            'quantity' => 10,
            'unit_cost_local' => 100,
            'unit_sale_price_local' => 0,
            'type' => 'purchase_in',
        ]);

        $this->assertSame(0, AppNotification::query()->count());

        $service->recordMovement([
            'product_id' => $product->id,
            'from_location_id' => $location->id,
            'quantity' => 6,
            'unit_cost_local' => 0,
            'unit_sale_price_local' => 0,
            'type' => 'sale_out',
        ]);

        $this->assertSame(2, AppNotification::query()->whereNull('read_at')->count());
        $this->assertDatabaseHas('app_notifications', [
            'title' => 'Stock bas detecte',
            'fingerprint' => 'low-stock:' . $product->id . ':' . $location->id,
        ]);

        $service->recordMovement([
            'product_id' => $product->id,
            'to_location_id' => $location->id,
            'quantity' => 5,
            'unit_cost_local' => 100,
            'unit_sale_price_local' => 0,
            'type' => 'purchase_in',
        ]);

        $this->assertSame(0, AppNotification::query()->whereNull('read_at')->count());
    }
}
