<?php

namespace Tests\Feature;

use App\Exports\SalesLinesExport;
use App\Livewire\Reports\Sales as SalesReport;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_report_builds_line_totals_and_profit(): void
    {
        $user = User::factory()->create(['role' => 'owner']);
        $location = StockLocation::query()->create(['code' => 'MAG-SR', 'name' => 'Magasin Rapport']);
        $product = Product::query()->create([
            'sku' => 'SKU-SR-001',
            'name' => 'Scie',
            'avg_cost_local' => 40,
            'sale_price_local' => 75,
            'is_active' => true,
        ]);

        $sale = Sale::query()->create([
            'type' => 'cash',
            'status' => 'paid',
            'subtotal' => 225,
            'discount_total' => 0,
            'total_amount' => 225,
            'paid_total' => 225,
            'sold_at' => now(),
        ]);

        SaleItem::query()->create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'location_id' => $location->id,
            'quantity' => 3,
            'unit_price' => 75,
            'unit_cost_local' => 40,
            'discount_amount' => 0,
            'line_total' => 225,
        ]);

        $this->actingAs($user);

        $view = app(SalesReport::class)->render();
        $totals = $view->getData()['totals'];

        $this->assertSame(1, $totals['lines']);
        $this->assertSame(3.0, $totals['quantity']);
        $this->assertSame(225.0, $totals['sales']);
        $this->assertSame(120.0, $totals['purchase']);
        $this->assertSame(105.0, $totals['profit']);
    }

    public function test_sales_report_export_contains_expected_columns_and_profit(): void
    {
        $user = User::factory()->create(['role' => 'owner']);
        $location = StockLocation::query()->create(['code' => 'MAG-SRE', 'name' => 'Magasin Export']);
        $product = Product::query()->create([
            'sku' => 'SKU-SR-002',
            'name' => 'Cle plate',
            'avg_cost_local' => 10,
            'sale_price_local' => 18,
            'is_active' => true,
        ]);

        $sale = Sale::query()->create([
            'type' => 'cash',
            'status' => 'paid',
            'subtotal' => 36,
            'discount_total' => 0,
            'total_amount' => 36,
            'paid_total' => 36,
            'sold_at' => now(),
        ]);

        SaleItem::query()->create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'location_id' => $location->id,
            'quantity' => 2,
            'unit_price' => 18,
            'unit_cost_local' => 10,
            'discount_amount' => 0,
            'line_total' => 36,
        ]);

        $this->actingAs($user);

        $export = new SalesLinesExport();

        $this->assertSame(
            ['Vente', 'Date', 'Client', 'Magasin', 'Article', 'Quantite', 'Prix vente unitaire', 'Prix achat unitaire', 'Total vente', 'Total achat', 'Benefice'],
            $export->headings()
        );

        $row = $export->collection()->first();

        $this->assertSame($sale->id, $row[0]);
        $this->assertSame('Magasin Export', $row[3]);
        $this->assertSame('Cle plate', $row[4]);
        $this->assertSame(36.0, $row[8]);
        $this->assertSame(20.0, $row[9]);
        $this->assertSame(16.0, $row[10]);
    }
}
