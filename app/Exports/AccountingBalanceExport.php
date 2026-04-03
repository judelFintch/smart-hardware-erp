<?php

namespace App\Exports;

use App\Models\JournalEntryLine;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AccountingBalanceExport implements FromCollection, WithHeadings
{
    public function __construct(
        private readonly ?string $start = null,
        private readonly ?string $end = null,
    ) {
    }

    public function headings(): array
    {
        return ['Compte', 'Libelle', 'Type', 'Debit', 'Credit', 'Solde'];
    }

    public function collection(): Collection
    {
        return $this->query()
            ->select([
                'accounts.number',
                'accounts.name',
                'accounts.type',
                DB::raw('SUM(journal_entry_lines.debit) as total_debit'),
                DB::raw('SUM(journal_entry_lines.credit) as total_credit'),
            ])
            ->join('accounts', 'accounts.id', '=', 'journal_entry_lines.account_id')
            ->groupBy('accounts.id', 'accounts.number', 'accounts.name', 'accounts.type')
            ->orderBy('accounts.number')
            ->get()
            ->map(function ($row) {
                $debit = (float) ($row->total_debit ?? 0);
                $credit = (float) ($row->total_credit ?? 0);

                return [
                    $row->number,
                    $row->name,
                    $row->type,
                    $debit,
                    $credit,
                    $debit - $credit,
                ];
            });
    }

    private function query(): Builder
    {
        return JournalEntryLine::query()
            ->whereHas('entry', function (Builder $query) {
                if ($this->start) {
                    $query->whereDate('entry_date', '>=', $this->start);
                }
                if ($this->end) {
                    $query->whereDate('entry_date', '<=', $this->end);
                }
            });
    }
}
