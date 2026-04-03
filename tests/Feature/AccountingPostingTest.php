<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\AccountingSetting;
use App\Models\Expense;
use App\Models\InventoryCount;
use App\Models\InventoryCountItem;
use App\Models\JournalEntry;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\StockLocation;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use App\Services\AccountingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountingPostingTest extends TestCase
{
    use RefreshDatabase;

    public function test_sale_is_automatically_posted_to_accounting(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'owner']));

        $unit = Unit::create([
            'code' => 'pcs',
            'name' => 'Pieces',
            'type' => 'piece',
        ]);
        $location = StockLocation::create([
            'code' => 'ACC-SALE',
            'name' => 'Magasin compta',
        ]);
        $product = Product::create([
            'sku' => 'ACC-SALE-001',
            'name' => 'Article comptable',
            'unit_id' => $unit->id,
            'sale_margin_percent' => 0,
            'reorder_level' => 0,
            'is_active' => true,
        ]);

        $sale = Sale::create([
            'type' => 'cash',
            'status' => 'paid',
            'subtotal' => 0,
            'discount_total' => 0,
            'total_amount' => 0,
            'paid_total' => 0,
            'sold_at' => now(),
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'location_id' => $location->id,
            'quantity' => 3,
            'unit_price' => 50,
            'unit_cost_local' => 30,
            'discount_amount' => 0,
            'line_total' => 150,
        ]);

        $sale->update([
            'subtotal' => 150,
            'total_amount' => 150,
            'paid_total' => 150,
        ]);

        $entry = JournalEntry::query()
            ->where('source_type', Sale::class)
            ->where('source_id', $sale->id)
            ->first();

        $this->assertNotNull($entry);
        $this->assertSame('SALE-' . $sale->id, $entry->reference);
        $this->assertCount(4, $entry->lines);
        $this->assertSame(240.0, (float) ($entry->lines->sum('debit') + $entry->lines->sum('credit')) / 2);

        $this->assertDatabaseHas('journal_entry_lines', [
            'journal_entry_id' => $entry->id,
            'debit' => 150,
            'credit' => 0,
        ]);
        $this->assertDatabaseHas('journal_entry_lines', [
            'journal_entry_id' => $entry->id,
            'debit' => 0,
            'credit' => 150,
        ]);
        $this->assertDatabaseHas('journal_entry_lines', [
            'journal_entry_id' => $entry->id,
            'debit' => 90,
            'credit' => 0,
        ]);
        $this->assertDatabaseHas('journal_entry_lines', [
            'journal_entry_id' => $entry->id,
            'debit' => 0,
            'credit' => 90,
        ]);
    }

    public function test_received_purchase_is_automatically_posted_to_accounting(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'owner']));

        $supplier = Supplier::create(['name' => 'Fournisseur Compta']);
        $purchase = PurchaseOrder::create([
            'supplier_id' => $supplier->id,
            'type' => 'local',
            'status' => 'commande',
            'reference' => 'PO-001',
            'ordered_at' => now()->toDateString(),
            'currency' => 'CDF',
            'exchange_rate' => 1,
            'subtotal_foreign' => 0,
            'subtotal_local' => 0,
            'accessory_fees_local' => 0,
            'transport_fees_local' => 0,
            'total_cost_local' => 0,
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $purchase->id,
            'product_id' => Product::create([
                'sku' => 'ACC-PUR-001',
                'name' => 'Article achat',
                'sale_margin_percent' => 0,
                'reorder_level' => 0,
                'is_active' => true,
            ])->id,
            'quantity' => 2,
            'received_quantity' => 2,
            'unit_price_foreign' => 0,
            'unit_price_local' => 50,
            'line_total_foreign' => 0,
            'line_total_local' => 100,
            'unit_cost_local' => 50,
        ]);

        $purchase->update([
            'status' => 'approvisionnee',
            'subtotal_local' => 100,
            'total_cost_local' => 100,
            'received_at' => now()->toDateString(),
        ]);

        $entry = JournalEntry::query()
            ->where('source_type', PurchaseOrder::class)
            ->where('source_id', $purchase->id)
            ->first();

        $this->assertNotNull($entry);
        $this->assertSame('PO-001', $entry->reference);
        $this->assertCount(2, $entry->lines);
        $this->assertDatabaseHas('journal_entry_lines', [
            'journal_entry_id' => $entry->id,
            'debit' => 100,
            'credit' => 0,
        ]);
        $this->assertDatabaseHas('journal_entry_lines', [
            'journal_entry_id' => $entry->id,
            'debit' => 0,
            'credit' => 100,
        ]);
    }

    public function test_expense_is_automatically_posted_to_accounting(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'owner']));

        $expense = Expense::create([
            'category' => 'transport',
            'description' => 'Taxi livraison',
            'amount' => 25,
            'spent_at' => now()->toDateString(),
            'reference' => 'EXP-001',
        ]);

        $entry = JournalEntry::query()
            ->where('source_type', Expense::class)
            ->where('source_id', $expense->id)
            ->first();

        $this->assertNotNull($entry);
        $this->assertSame('EXP-001', $entry->reference);
        $this->assertCount(2, $entry->lines);
        $this->assertDatabaseHas('journal_entry_lines', [
            'journal_entry_id' => $entry->id,
            'debit' => 25,
            'credit' => 0,
        ]);
        $this->assertDatabaseHas('journal_entry_lines', [
            'journal_entry_id' => $entry->id,
            'debit' => 0,
            'credit' => 25,
        ]);
    }

    public function test_sale_posting_uses_configured_accounting_setting(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'owner']));

        app(AccountingService::class)->ensureDefaults();

        $customCash = Account::create([
            'number' => '572',
            'name' => 'Banque',
            'type' => 'asset',
            'category' => 'treasury',
        ]);

        AccountingSetting::query()
            ->where('key', 'sale_cash_account')
            ->update(['account_id' => $customCash->id]);

        $sale = Sale::create([
            'type' => 'cash',
            'status' => 'paid',
            'subtotal' => 0,
            'discount_total' => 0,
            'total_amount' => 0,
            'paid_total' => 0,
            'sold_at' => now(),
        ]);

        $unit = Unit::create([
            'code' => 'cfg',
            'name' => 'Config',
            'type' => 'piece',
        ]);
        $location = StockLocation::create([
            'code' => 'CFG-LOC',
            'name' => 'Config location',
        ]);
        $product = Product::create([
            'sku' => 'CFG-SALE-001',
            'name' => 'Produit config',
            'unit_id' => $unit->id,
            'sale_margin_percent' => 0,
            'reorder_level' => 0,
            'is_active' => true,
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'location_id' => $location->id,
            'quantity' => 1,
            'unit_price' => 80,
            'unit_cost_local' => 50,
            'discount_amount' => 0,
            'line_total' => 80,
        ]);

        $sale->update([
            'subtotal' => 80,
            'total_amount' => 80,
            'paid_total' => 80,
        ]);

        $entry = JournalEntry::query()
            ->where('source_type', Sale::class)
            ->where('source_id', $sale->id)
            ->firstOrFail();

        $this->assertDatabaseHas('journal_entry_lines', [
            'journal_entry_id' => $entry->id,
            'account_id' => $customCash->id,
            'debit' => 80,
            'credit' => 0,
        ]);
    }

    public function test_credit_sale_payment_is_posted_to_treasury_and_receivable(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'owner']));

        app(AccountingService::class)->ensureDefaults();

        $sale = Sale::create([
            'type' => 'credit',
            'status' => 'open',
            'subtotal' => 100,
            'discount_total' => 0,
            'total_amount' => 100,
            'paid_total' => 0,
            'sold_at' => now(),
        ]);

        $payment = SalePayment::create([
            'sale_id' => $sale->id,
            'amount' => 40,
            'paid_at' => now()->toDateString(),
            'method' => 'cash',
            'reference' => 'PAY-001',
        ]);

        $entry = JournalEntry::query()
            ->where('source_type', SalePayment::class)
            ->where('source_id', $payment->id)
            ->firstOrFail();

        $this->assertSame('PAY-001', $entry->reference);
        $this->assertCount(2, $entry->lines);
        $this->assertDatabaseHas('journal_entry_lines', [
            'journal_entry_id' => $entry->id,
            'debit' => 40,
            'credit' => 0,
        ]);
        $this->assertDatabaseHas('journal_entry_lines', [
            'journal_entry_id' => $entry->id,
            'debit' => 0,
            'credit' => 40,
        ]);
    }

    public function test_inventory_count_posts_gain_and_loss_accounts(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'owner']));

        app(AccountingService::class)->ensureDefaults();

        $location = StockLocation::create([
            'code' => 'INV-CPT',
            'name' => 'Depot inventaire',
        ]);
        $unit = Unit::create([
            'code' => 'inv',
            'name' => 'Inventaire',
            'type' => 'piece',
        ]);
        $product = Product::create([
            'sku' => 'INV-001',
            'name' => 'Produit inventaire',
            'unit_id' => $unit->id,
            'sale_margin_percent' => 0,
            'reorder_level' => 0,
            'is_active' => true,
        ]);

        $inventory = InventoryCount::create([
            'location_id' => $location->id,
            'counted_at' => now()->toDateString(),
            'created_by' => auth()->id(),
        ]);

        InventoryCountItem::create([
            'inventory_count_id' => $inventory->id,
            'product_id' => $product->id,
            'counted_quantity' => 12,
            'system_quantity' => 10,
            'difference' => 2,
            'unit_cost_local' => 5,
            'unit_sale_price_local' => 8,
        ]);

        InventoryCountItem::create([
            'inventory_count_id' => $inventory->id,
            'product_id' => $product->id,
            'counted_quantity' => 8,
            'system_quantity' => 10,
            'difference' => -2,
            'unit_cost_local' => 4,
            'unit_sale_price_local' => 8,
        ]);

        $inventory->update([
            'notes' => 'Validation inventaire',
        ]);

        $entry = JournalEntry::query()
            ->where('source_type', InventoryCount::class)
            ->where('source_id', $inventory->id)
            ->firstOrFail();

        $this->assertCount(4, $entry->lines);
        $this->assertDatabaseHas('journal_entry_lines', [
            'journal_entry_id' => $entry->id,
            'debit' => 10,
            'credit' => 0,
        ]);
        $this->assertDatabaseHas('journal_entry_lines', [
            'journal_entry_id' => $entry->id,
            'debit' => 0,
            'credit' => 10,
        ]);
        $this->assertDatabaseHas('journal_entry_lines', [
            'journal_entry_id' => $entry->id,
            'debit' => 8,
            'credit' => 0,
        ]);
        $this->assertDatabaseHas('journal_entry_lines', [
            'journal_entry_id' => $entry->id,
            'debit' => 0,
            'credit' => 8,
        ]);
    }
}
