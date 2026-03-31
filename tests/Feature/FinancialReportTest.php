<?php

namespace Tests\Feature;

use App\Livewire\Reports\Financial;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_profit_can_be_calculated_from_applied_sale_price(): void
    {
        $user = User::factory()->create(['role' => 'owner']);
        $location = StockLocation::query()->create(['code' => 'MAG-RPT', 'name' => 'Magasin Rapport']);
        $product = Product::query()->create([
            'sku' => 'SKU-RPT-001',
            'name' => 'Perceuse',
            'sale_price_local' => 150,
            'sale_margin_percent' => 40,
            'avg_cost_local' => 80,
            'is_active' => true,
        ]);

        $sale = Sale::query()->create([
            'type' => 'cash',
            'status' => 'paid',
            'subtotal' => 300,
            'discount_total' => 20,
            'total_amount' => 280,
            'paid_total' => 280,
            'sold_at' => now(),
        ]);

        SaleItem::query()->create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'location_id' => $location->id,
            'quantity' => 2,
            'unit_price' => 150,
            'unit_cost_local' => 80,
            'discount_amount' => 0,
            'line_total' => 300,
        ]);

        Expense::query()->create([
            'category' => 'Transport',
            'description' => 'Livraison client',
            'amount' => 10,
            'spent_at' => now()->toDateString(),
        ]);

        $this->actingAs($user);

        $component = app(Financial::class);

        $appliedSalePriceView = $component->render();
        $this->assertSame(130.0, $appliedSalePriceView->getData()['profit']);

        $component->profitCalculationMode = 'net_sales';
        $netSalesView = $component->render();
        $this->assertSame(110.0, $netSalesView->getData()['profit']);
    }
}
