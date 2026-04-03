<?php

namespace App\Exports;

use App\Support\AccountingStatementBuilder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AccountingIncomeStatementExport implements FromCollection, WithHeadings
{
    public function __construct(
        private readonly ?string $start = null,
        private readonly ?string $end = null,
    ) {
    }

    public function headings(): array
    {
        return ['Section', 'Compte', 'Libelle', 'Montant'];
    }

    public function collection(): Collection
    {
        $statement = app(AccountingStatementBuilder::class)->incomeStatement($this->start, $this->end);

        return collect([
            ['Produits d exploitation', $statement['sections']['operating_revenue']],
            ['Charges d exploitation', $statement['sections']['operating_expense']],
            ['Produits hors activites ordinaires', $statement['sections']['other_revenue']],
            ['Charges hors activites ordinaires', $statement['sections']['other_expense']],
        ])->flatMap(function (array $section) {
            [$label, $rows] = $section;

            return $rows->map(fn (array $row) => [$label, $row['number'], $row['name'], $row['amount']]);
        })->push(
            ['Resultat net', '', '', $statement['totals']['net_result']]
        );
    }
}
