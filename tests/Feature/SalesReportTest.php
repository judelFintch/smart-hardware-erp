<?php

namespace Tests\Feature;

use App\Exports\SalesLinesExport;
use App\Livewire\Reports\Sales as SalesReport;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockBalance;
use App\Models\StockLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_report_builds_product_totals_and_profit(): void
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

        StockBalance::query()->create([
            'product_id' => $product->id,
            'location_id' => $location->id,
            'quantity' => 7,
            'avg_cost_local' => 40,
            'sale_price_local' => 75,
        ]);

        $this->actingAs($user);

        $view = app(SalesReport::class)->render();
        $totals = $view->getData()['totals'];
        $saleLines = $view->getData()['saleLines'];

        $this->assertSame(1, $totals['products']);
        $this->assertSame(3.0, $totals['quantity']);
        $this->assertSame(225.0, $totals['sales']);
        $this->assertSame(120.0, $totals['purchase']);
        $this->assertSame(105.0, $totals['profit']);

        $row = $saleLines->getCollection()->first();

        $this->assertSame(3.0, (float) $row->quantity_sold);
        $this->assertSame(75.0, (float) $row->unit_sale_price_avg);
        $this->assertSame(40.0, (float) $row->unit_purchase_price_avg);
        $this->assertSame(120.0, (float) $row->purchase_total);
        $this->assertSame(105.0, (float) $row->profit_total);
        $this->assertSame(7.0, (float) $row->remaining_quantity);
        $this->assertSame(280.0, (float) $row->remaining_valuation);
    }

    public function test_sales_report_export_contains_stock_and_profit_columns(): void
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

        StockBalance::query()->create([
            'product_id' => $product->id,
            'location_id' => $location->id,
            'quantity' => 5,
            'avg_cost_local' => 10,
            'sale_price_local' => 18,
        ]);

        $this->actingAs($user);

        $export = new SalesLinesExport();

        $this->assertSame(
            ['Article', 'SKU', 'Quantite vendue', 'Prix vente unitaire moyen', 'Prix achat unitaire moyen', 'Total vente', 'Total achat', 'Benefice', 'Quantite restante', 'Valorisation stock'],
            $export->headings()
        );

        $row = $export->collection()->first();

        $this->assertSame('Cle plate', $row[0]);
        $this->assertSame('SKU-SR-002', $row[1]);
        $this->assertSame('2', $row[2]);
        $this->assertSame(18.0, $row[3]);
        $this->assertSame(10.0, $row[4]);
        $this->assertSame(36.0, $row[5]);
        $this->assertSame(20.0, $row[6]);
        $this->assertSame(16.0, $row[7]);
        $this->assertSame('5', $row[8]);
        $this->assertSame(50.0, $row[9]);
    }
}
