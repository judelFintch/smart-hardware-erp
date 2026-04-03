<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AccountingSetting;
use App\Models\Expense;
use App\Models\InventoryCount;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\PurchaseOrder;
use App\Models\Sale;
use App\Models\SalePayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    public function forget(Model $source): ?JournalEntry
    {
        return $this->deleteEntryFor($source);
    }

    public function postSale(Sale $sale): ?JournalEntry
    {
        $sale->load('items');

        if ((float) $sale->total_amount <= 0 || !in_array($sale->status, ['open', 'paid'], true)) {
            return $this->deleteEntryFor($sale);
        }

        $defaults = $this->ensureDefaults();
        $settings = $defaults['settings'];
        $salesTotal = round((float) $sale->total_amount, 2);
        $costTotal = round((float) $sale->items->sum(fn ($item) => (float) $item->unit_cost_local * (float) $item->quantity), 2);

        return $this->storeEntry(
            source: $sale,
            journal: $settings['sales_journal']->journal ?? $defaults['journals']['sales'],
            entryDate: $sale->sold_at?->toDateString() ?? now()->toDateString(),
            reference: 'SALE-' . $sale->id,
            description: 'Imputation automatique vente #' . $sale->id,
            lines: array_values(array_filter([
                [
                    'account' => $sale->type === 'cash'
                        ? ($settings['sale_cash_account']->account ?? $defaults['accounts']['cash'])
                        : ($settings['sale_credit_account']->account ?? $defaults['accounts']['receivable']),
                    'debit' => $salesTotal,
                    'credit' => 0,
                    'description' => 'Encaissement / creance client',
                ],
                [
                    'account' => $settings['sale_revenue_account']->account ?? $defaults['accounts']['sales'],
                    'debit' => 0,
                    'credit' => $salesTotal,
                    'description' => 'Produit de vente',
                ],
                $costTotal > 0 ? [
                    'account' => $settings['sale_cogs_account']->account ?? $defaults['accounts']['cogs'],
                    'debit' => $costTotal,
                    'credit' => 0,
                    'description' => 'Cout des marchandises vendues',
                ] : null,
                $costTotal > 0 ? [
                    'account' => $settings['inventory_account']->account ?? $defaults['accounts']['stock'],
                    'debit' => 0,
                    'credit' => $costTotal,
                    'description' => 'Sortie de stock',
                ] : null,
            ])),
            meta: [
                'source' => 'sale',
                'sale_type' => $sale->type,
                'sale_status' => $sale->status,
            ],
        );
    }

    public function postPurchase(PurchaseOrder $purchase): ?JournalEntry
    {
        if ((float) $purchase->total_cost_local <= 0 || !in_array($purchase->status, ['receptionnee', 'approvisionnee'], true)) {
            return $this->deleteEntryFor($purchase);
        }

        $defaults = $this->ensureDefaults();
        $settings = $defaults['settings'];
        $amount = round((float) $purchase->total_cost_local, 2);

        return $this->storeEntry(
            source: $purchase,
            journal: $settings['purchases_journal']->journal ?? $defaults['journals']['purchases'],
            entryDate: $purchase->received_at?->toDateString() ?? $purchase->ordered_at?->toDateString() ?? now()->toDateString(),
            reference: $purchase->reference ?: 'PURCHASE-' . $purchase->id,
            description: 'Imputation automatique achat #' . $purchase->id,
            lines: [
                [
                    'account' => $settings['inventory_account']->account ?? $defaults['accounts']['stock'],
                    'debit' => $amount,
                    'credit' => 0,
                    'description' => 'Entree en stock',
                ],
                [
                    'account' => $settings['supplier_account']->account ?? $defaults['accounts']['supplier'],
                    'debit' => 0,
                    'credit' => $amount,
                    'description' => 'Dette fournisseur',
                ],
            ],
            meta: [
                'source' => 'purchase',
                'purchase_status' => $purchase->status,
            ],
        );
    }

    public function postExpense(Expense $expense): ?JournalEntry
    {
        if ((float) $expense->amount <= 0) {
            return $this->deleteEntryFor($expense);
        }

        $defaults = $this->ensureDefaults();
        $settings = $defaults['settings'];
        $amount = round((float) $expense->amount, 2);

        return $this->storeEntry(
            source: $expense,
            journal: $settings['expenses_journal']->journal ?? $defaults['journals']['expenses'],
            entryDate: $expense->spent_at?->toDateString() ?? now()->toDateString(),
            reference: $expense->reference ?: 'EXPENSE-' . $expense->id,
            description: 'Imputation automatique depense #' . $expense->id,
            lines: [
                [
                    'account' => $this->resolveExpenseAccount((string) $expense->category, $defaults),
                    'debit' => $amount,
                    'credit' => 0,
                    'description' => $expense->description,
                ],
                [
                    'account' => $settings['sale_cash_account']->account ?? $defaults['accounts']['cash'],
                    'debit' => 0,
                    'credit' => $amount,
                    'description' => 'Reglement depense',
                ],
            ],
            meta: [
                'source' => 'expense',
                'category' => $expense->category,
            ],
        );
    }

    public function postSalePayment(SalePayment $payment): ?JournalEntry
    {
        $payment->loadMissing('sale');

        if (! $payment->sale || (float) $payment->amount <= 0 || $payment->sale->type !== 'credit') {
            return $this->deleteEntryFor($payment);
        }

        $defaults = $this->ensureDefaults();
        $settings = $defaults['settings'];
        $amount = round((float) $payment->amount, 2);
        $cashOrBankAccount = $this->resolveTreasuryAccount((string) $payment->method, $defaults);

        return $this->storeEntry(
            source: $payment,
            journal: $settings['sales_journal']->journal ?? $defaults['journals']['sales'],
            entryDate: $payment->paid_at?->toDateString() ?? now()->toDateString(),
            reference: $payment->reference ?: 'PAYMENT-' . $payment->id,
            description: 'Imputation automatique reglement vente #' . $payment->sale_id,
            lines: [
                [
                    'account' => $cashOrBankAccount,
                    'debit' => $amount,
                    'credit' => 0,
                    'description' => 'Encaissement reglement client',
                ],
                [
                    'account' => $settings['sale_credit_account']->account ?? $defaults['accounts']['receivable'],
                    'debit' => 0,
                    'credit' => $amount,
                    'description' => 'Diminution creance client',
                ],
            ],
            meta: [
                'source' => 'sale_payment',
                'sale_id' => $payment->sale_id,
                'payment_method' => $payment->method,
            ],
        );
    }

    public function postInventoryCount(InventoryCount $inventoryCount): ?JournalEntry
    {
        $inventoryCount->load('items');

        if ($inventoryCount->items->isEmpty()) {
            return $this->deleteEntryFor($inventoryCount);
        }

        $defaults = $this->ensureDefaults();
        $settings = $defaults['settings'];

        $positiveAmount = round((float) $inventoryCount->items
            ->filter(fn ($item) => (float) $item->difference > 0)
            ->sum(fn ($item) => (float) $item->difference * (float) $item->unit_cost_local), 2);
        $negativeAmount = round((float) $inventoryCount->items
            ->filter(fn ($item) => (float) $item->difference < 0)
            ->sum(fn ($item) => abs((float) $item->difference) * (float) $item->unit_cost_local), 2);

        if ($positiveAmount <= 0 && $negativeAmount <= 0) {
            return $this->deleteEntryFor($inventoryCount);
        }

        return $this->storeEntry(
            source: $inventoryCount,
            journal: $settings['inventory_journal']->journal ?? $defaults['journals']['inventory'],
            entryDate: $inventoryCount->counted_at?->toDateString() ?? now()->toDateString(),
            reference: 'INV-' . $inventoryCount->id,
            description: 'Imputation automatique ecart inventaire #' . $inventoryCount->id,
            lines: array_values(array_filter([
                $positiveAmount > 0 ? [
                    'account' => $settings['inventory_account']->account ?? $defaults['accounts']['stock'],
                    'debit' => $positiveAmount,
                    'credit' => 0,
                    'description' => 'Regularisation positive de stock',
                ] : null,
                $positiveAmount > 0 ? [
                    'account' => $settings['inventory_gain_account']->account ?? $defaults['accounts']['inventory_gain'],
                    'debit' => 0,
                    'credit' => $positiveAmount,
                    'description' => 'Gain inventaire',
                ] : null,
                $negativeAmount > 0 ? [
                    'account' => $settings['inventory_loss_account']->account ?? $defaults['accounts']['inventory_loss'],
                    'debit' => $negativeAmount,
                    'credit' => 0,
                    'description' => 'Perte inventaire',
                ] : null,
                $negativeAmount > 0 ? [
                    'account' => $settings['inventory_account']->account ?? $defaults['accounts']['stock'],
                    'debit' => 0,
                    'credit' => $negativeAmount,
                    'description' => 'Regularisation negative de stock',
                ] : null,
            ])),
            meta: [
                'source' => 'inventory_count',
                'positive_amount' => $positiveAmount,
                'negative_amount' => $negativeAmount,
            ],
        );
    }

    private function storeEntry(
        Model $source,
        Journal $journal,
        string $entryDate,
        ?string $reference,
        string $description,
        array $lines,
        array $meta = [],
    ): JournalEntry {
        return DB::transaction(function () use ($source, $journal, $entryDate, $reference, $description, $lines, $meta) {
            $entry = JournalEntry::query()->updateOrCreate(
                [
                    'source_type' => $source::class,
                    'source_id' => $source->getKey(),
                ],
                [
                    'journal_id' => $journal->id,
                    'user_id' => auth()->id(),
                    'entry_date' => $entryDate,
                    'reference' => $reference,
                    'description' => $description,
                    'status' => 'posted',
                    'posted_at' => now(),
                    'meta' => $meta,
                ]
            );

            $entry->lines()->delete();

            foreach (array_values($lines) as $index => $line) {
                $entry->lines()->create([
                    'account_id' => $line['account']->id,
                    'description' => $line['description'] ?? null,
                    'debit' => round((float) ($line['debit'] ?? 0), 2),
                    'credit' => round((float) ($line['credit'] ?? 0), 2),
                    'line_order' => $index + 1,
                ]);
            }

            return $entry->fresh('lines.account');
        });
    }

    private function deleteEntryFor(Model $source): ?JournalEntry
    {
        $entry = JournalEntry::query()
            ->where('source_type', $source::class)
            ->where('source_id', $source->getKey())
            ->first();

        if (!$entry) {
            return null;
        }

        DB::transaction(function () use ($entry) {
            $entry->lines()->delete();
            $entry->delete();
        });

        return null;
    }

    public function ensureDefaults(): array
    {
        $accounts = [
            'cash' => Account::firstOrCreate(['number' => '571'], ['name' => 'Caisse', 'type' => 'asset', 'category' => 'treasury', 'is_system' => true]),
            'receivable' => Account::firstOrCreate(['number' => '411'], ['name' => 'Clients', 'type' => 'asset', 'category' => 'receivable', 'is_system' => true]),
            'supplier' => Account::firstOrCreate(['number' => '401'], ['name' => 'Fournisseurs', 'type' => 'liability', 'category' => 'payable', 'is_system' => true]),
            'sales' => Account::firstOrCreate(['number' => '701'], ['name' => 'Ventes de marchandises', 'type' => 'revenue', 'category' => 'sales', 'is_system' => true]),
            'stock' => Account::firstOrCreate(['number' => '31'], ['name' => 'Stocks de marchandises', 'type' => 'asset', 'category' => 'inventory', 'is_system' => true]),
            'cogs' => Account::firstOrCreate(['number' => '603'], ['name' => 'Variation des stocks', 'type' => 'expense', 'category' => 'cost_of_sales', 'is_system' => true]),
            'expense' => Account::firstOrCreate(['number' => '61'], ['name' => 'Services exterieurs', 'type' => 'expense', 'category' => 'general_expense', 'is_system' => true]),
            'transport' => Account::firstOrCreate(['number' => '624'], ['name' => 'Transport', 'type' => 'expense', 'category' => 'transport', 'is_system' => true]),
            'utilities' => Account::firstOrCreate(['number' => '627'], ['name' => 'Frais bancaires et services assimilés', 'type' => 'expense', 'category' => 'utilities', 'is_system' => true]),
            'rent' => Account::firstOrCreate(['number' => '622'], ['name' => 'Locations', 'type' => 'expense', 'category' => 'rent', 'is_system' => true]),
            'bank' => Account::firstOrCreate(['number' => '521'], ['name' => 'Banques locales', 'type' => 'asset', 'category' => 'bank_local', 'is_system' => true]),
            'inventory_gain' => Account::firstOrCreate(['number' => '781'], ['name' => 'Reprises sur regularisation de stock', 'type' => 'revenue', 'category' => 'inventory_gain', 'is_system' => true]),
            'inventory_loss' => Account::firstOrCreate(['number' => '681'], ['name' => 'Charges de regularisation de stock', 'type' => 'expense', 'category' => 'inventory_loss', 'is_system' => true]),
        ];

        $journals = [
            'sales' => Journal::firstOrCreate(['code' => 'VEN'], ['name' => 'Journal des ventes', 'type' => 'sales', 'is_system' => true]),
            'purchases' => Journal::firstOrCreate(['code' => 'ACH'], ['name' => 'Journal des achats', 'type' => 'purchases', 'is_system' => true]),
            'expenses' => Journal::firstOrCreate(['code' => 'OD'], ['name' => 'Journal des operations diverses', 'type' => 'general', 'is_system' => true]),
            'inventory' => Journal::firstOrCreate(['code' => 'INV'], ['name' => 'Journal des inventaires', 'type' => 'inventory', 'is_system' => true]),
        ];

        $settings = [
            'sale_cash_account' => AccountingSetting::firstOrCreate(
                ['key' => 'sale_cash_account'],
                [
                    'label' => 'Compte vente comptant',
                    'group' => 'sales',
                    'value_type' => 'account',
                    'description' => 'Compte debite lors d une vente comptant.',
                    'account_id' => $accounts['cash']->id,
                ]
            ),
            'sale_credit_account' => AccountingSetting::firstOrCreate(
                ['key' => 'sale_credit_account'],
                [
                    'label' => 'Compte vente a credit',
                    'group' => 'sales',
                    'value_type' => 'account',
                    'description' => 'Compte debite lors d une vente a credit.',
                    'account_id' => $accounts['receivable']->id,
                ]
            ),
            'sale_revenue_account' => AccountingSetting::firstOrCreate(
                ['key' => 'sale_revenue_account'],
                [
                    'label' => 'Compte produit des ventes',
                    'group' => 'sales',
                    'value_type' => 'account',
                    'description' => 'Compte credite pour le chiffre d affaires.',
                    'account_id' => $accounts['sales']->id,
                ]
            ),
            'sale_cogs_account' => AccountingSetting::firstOrCreate(
                ['key' => 'sale_cogs_account'],
                [
                    'label' => 'Compte cout des ventes',
                    'group' => 'sales',
                    'value_type' => 'account',
                    'description' => 'Compte debite pour le cout des marchandises vendues.',
                    'account_id' => $accounts['cogs']->id,
                ]
            ),
            'inventory_account' => AccountingSetting::firstOrCreate(
                ['key' => 'inventory_account'],
                [
                    'label' => 'Compte de stock',
                    'group' => 'inventory',
                    'value_type' => 'account',
                    'description' => 'Compte de stock utilise pour les mouvements valorises.',
                    'account_id' => $accounts['stock']->id,
                ]
            ),
            'supplier_account' => AccountingSetting::firstOrCreate(
                ['key' => 'supplier_account'],
                [
                    'label' => 'Compte fournisseur',
                    'group' => 'purchases',
                    'value_type' => 'account',
                    'description' => 'Compte credite lors d un achat receptionne non regle.',
                    'account_id' => $accounts['supplier']->id,
                ]
            ),
            'bank_account' => AccountingSetting::firstOrCreate(
                ['key' => 'bank_account'],
                [
                    'label' => 'Compte banque',
                    'group' => 'treasury',
                    'value_type' => 'account',
                    'description' => 'Compte utilise pour les encaissements ou decaissements bancaires.',
                    'account_id' => $accounts['bank']->id,
                ]
            ),
            'expense_default_account' => AccountingSetting::firstOrCreate(
                ['key' => 'expense_default_account'],
                [
                    'label' => 'Compte charge par defaut',
                    'group' => 'expenses',
                    'value_type' => 'account',
                    'description' => 'Compte de charge utilise si aucune categorie specifique n est reconnue.',
                    'account_id' => $accounts['expense']->id,
                ]
            ),
            'expense_transport_account' => AccountingSetting::firstOrCreate(
                ['key' => 'expense_transport_account'],
                [
                    'label' => 'Compte charge transport',
                    'group' => 'expenses',
                    'value_type' => 'account',
                    'description' => 'Compte de charge utilise pour les depenses de transport.',
                    'account_id' => $accounts['transport']->id,
                ]
            ),
            'expense_utilities_account' => AccountingSetting::firstOrCreate(
                ['key' => 'expense_utilities_account'],
                [
                    'label' => 'Compte charge services',
                    'group' => 'expenses',
                    'value_type' => 'account',
                    'description' => 'Compte de charge utilise pour eau, electricite, banque et services assimilés.',
                    'account_id' => $accounts['utilities']->id,
                ]
            ),
            'expense_rent_account' => AccountingSetting::firstOrCreate(
                ['key' => 'expense_rent_account'],
                [
                    'label' => 'Compte charge loyer/location',
                    'group' => 'expenses',
                    'value_type' => 'account',
                    'description' => 'Compte de charge utilise pour les loyers et locations.',
                    'account_id' => $accounts['rent']->id,
                ]
            ),
            'inventory_gain_account' => AccountingSetting::firstOrCreate(
                ['key' => 'inventory_gain_account'],
                [
                    'label' => 'Compte gain inventaire',
                    'group' => 'inventory',
                    'value_type' => 'account',
                    'description' => 'Compte credite quand l inventaire revele un surplus.',
                    'account_id' => $accounts['inventory_gain']->id,
                ]
            ),
            'inventory_loss_account' => AccountingSetting::firstOrCreate(
                ['key' => 'inventory_loss_account'],
                [
                    'label' => 'Compte perte inventaire',
                    'group' => 'inventory',
                    'value_type' => 'account',
                    'description' => 'Compte debite quand l inventaire revele un manque.',
                    'account_id' => $accounts['inventory_loss']->id,
                ]
            ),
            'sales_journal' => AccountingSetting::firstOrCreate(
                ['key' => 'sales_journal'],
                [
                    'label' => 'Journal des ventes',
                    'group' => 'sales',
                    'value_type' => 'journal',
                    'description' => 'Journal utilise pour les ecritures de vente.',
                    'journal_id' => $journals['sales']->id,
                ]
            ),
            'purchases_journal' => AccountingSetting::firstOrCreate(
                ['key' => 'purchases_journal'],
                [
                    'label' => 'Journal des achats',
                    'group' => 'purchases',
                    'value_type' => 'journal',
                    'description' => 'Journal utilise pour les ecritures d achat.',
                    'journal_id' => $journals['purchases']->id,
                ]
            ),
            'expenses_journal' => AccountingSetting::firstOrCreate(
                ['key' => 'expenses_journal'],
                [
                    'label' => 'Journal des charges',
                    'group' => 'expenses',
                    'value_type' => 'journal',
                    'description' => 'Journal utilise pour les ecritures de depense.',
                    'journal_id' => $journals['expenses']->id,
                ]
            ),
            'inventory_journal' => AccountingSetting::firstOrCreate(
                ['key' => 'inventory_journal'],
                [
                    'label' => 'Journal des inventaires',
                    'group' => 'inventory',
                    'value_type' => 'journal',
                    'description' => 'Journal utilise pour les ecarts d inventaire.',
                    'journal_id' => $journals['inventory']->id,
                ]
            ),
        ];

        return [
            'accounts' => $accounts,
            'journals' => $journals,
            'settings' => $settings,
        ];
    }

    private function resolveExpenseAccount(string $category, array $defaults): Account
    {
        $normalized = mb_strtolower(trim($category));
        $accounts = $defaults['accounts'];
        $settings = $defaults['settings'];

        return match (true) {
            str_contains($normalized, 'transport') => $settings['expense_transport_account']->account ?? $accounts['transport'],
            str_contains($normalized, 'loyer') || str_contains($normalized, 'location') => $settings['expense_rent_account']->account ?? $accounts['rent'],
            str_contains($normalized, 'banque') || str_contains($normalized, 'eau') || str_contains($normalized, 'electric') => $settings['expense_utilities_account']->account ?? $accounts['utilities'],
            default => $settings['expense_default_account']->account ?? $accounts['expense'],
        };
    }

    private function resolveTreasuryAccount(string $method, array $defaults): Account
    {
        $normalized = mb_strtolower(trim($method));
        $settings = $defaults['settings'];
        $accounts = $defaults['accounts'];

        if (str_contains($normalized, 'banque') || str_contains($normalized, 'virement') || str_contains($normalized, 'bank')) {
            return $settings['bank_account']->account ?? $accounts['bank'];
        }

        return $settings['sale_cash_account']->account ?? $accounts['cash'];
    }
}
