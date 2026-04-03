<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Services\AccountingService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountingReportsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_accounting_report_pages_are_accessible(): void
    {
        $user = User::factory()->create(['role' => 'owner']);
        $this->actingAs($user);

        app(AccountingService::class)->ensureDefaults();

        $this->get(route('accounting.balance'))
            ->assertOk()
            ->assertSee('Balance comptable');

        $this->get(route('accounting.income-statement'))
            ->assertOk()
            ->assertSee('Compte de résultat');

        $this->get(route('accounting.balance-sheet'))
            ->assertOk()
            ->assertSee('Bilan simplifié');

        $this->get(route('accounting.ledger'))
            ->assertOk()
            ->assertSee('Grand livre');

        $this->get(route('accounting.journal-book'))
            ->assertOk()
            ->assertSee('Brouillard / journal');
    }

    public function test_income_statement_and_balance_sheet_show_expected_amounts(): void
    {
        $user = User::factory()->create(['role' => 'owner']);
        $this->actingAs($user);

        $journal = Journal::create([
            'code' => 'OD',
            'name' => 'Operations diverses',
            'type' => 'general',
            'is_system' => false,
        ]);

        $cash = Account::create([
            'number' => '571',
            'name' => 'Caisse',
            'type' => 'asset',
            'category' => 'cash_main',
            'is_system' => false,
        ]);

        $sales = Account::create([
            'number' => '701',
            'name' => 'Ventes de marchandises',
            'type' => 'revenue',
            'category' => 'sales_merchandise',
            'is_system' => false,
        ]);

        $purchases = Account::create([
            'number' => '601',
            'name' => 'Achats de marchandises',
            'type' => 'expense',
            'category' => 'purchases_merchandise',
            'is_system' => false,
        ]);

        $saleEntry = JournalEntry::create([
            'journal_id' => $journal->id,
            'user_id' => $user->id,
            'source_type' => 'tests.manual',
            'source_id' => 1,
            'entry_date' => '2026-04-03',
            'reference' => 'VT-001',
            'description' => 'Vente test',
            'status' => 'posted',
        ]);

        JournalEntryLine::create([
            'journal_entry_id' => $saleEntry->id,
            'account_id' => $cash->id,
            'debit' => 1000,
            'credit' => 0,
            'line_order' => 1,
        ]);

        JournalEntryLine::create([
            'journal_entry_id' => $saleEntry->id,
            'account_id' => $sales->id,
            'debit' => 0,
            'credit' => 1000,
            'line_order' => 2,
        ]);

        $purchaseEntry = JournalEntry::create([
            'journal_id' => $journal->id,
            'user_id' => $user->id,
            'source_type' => 'tests.manual',
            'source_id' => 2,
            'entry_date' => '2026-04-03',
            'reference' => 'AC-001',
            'description' => 'Achat test',
            'status' => 'posted',
        ]);

        JournalEntryLine::create([
            'journal_entry_id' => $purchaseEntry->id,
            'account_id' => $purchases->id,
            'debit' => 400,
            'credit' => 0,
            'line_order' => 1,
        ]);

        JournalEntryLine::create([
            'journal_entry_id' => $purchaseEntry->id,
            'account_id' => $cash->id,
            'debit' => 0,
            'credit' => 400,
            'line_order' => 2,
        ]);

        $this->get(route('accounting.income-statement', ['start' => '2026-04-01', 'end' => '2026-04-30']))
            ->assertOk()
            ->assertSee('1,000.00')
            ->assertSee('400.00')
            ->assertSee('600.00');

        $this->get(route('accounting.balance-sheet', ['start' => '2026-04-01', 'end' => '2026-04-30']))
            ->assertOk()
            ->assertSee('Caisse')
            ->assertSee('600.00')
            ->assertSee('Resultat net de la periode');
    }
}
