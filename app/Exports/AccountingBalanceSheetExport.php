<?php

namespace App\Exports;

use App\Support\AccountingStatementBuilder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AccountingBalanceSheetExport implements FromCollection, WithHeadings
{
    public function __construct(
        private readonly ?string $start = null,
        private readonly ?string $end = null,
    ) {
    }

    public function headings(): array
    {
        return ['Bloc', 'Compte', 'Libelle', 'Montant'];
    }

    public function collection(): Collection
    {
        $statement = app(AccountingStatementBuilder::class)->balanceSheet($this->start, $this->end);

        return collect([
            ['Actifs stables', $statement['sections']['stable_assets']],
            ['Stocks et encours', $statement['sections']['inventory_assets']],
            ['Creances et tresorerie', $statement['sections']['receivables_assets']],
            ['Capitaux propres', $statement['sections']['equity']],
            ['Dettes a long terme', $statement['sections']['long_term_liabilities']],
            ['Dettes a court terme', $statement['sections']['current_liabilities']],
        ])->flatMap(function (array $section) {
            [$label, $rows] = $section;

            return $rows->map(fn (array $row) => [$label, $row['number'], $row['name'], $row['amount']]);
        });
    }
}
