<?php

namespace App\Livewire\Accounting;

use App\Exports\AccountingBalanceExport;
use App\Models\CompanySetting;
use App\Models\JournalEntryLine;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class BalancePage extends Component
{
    public ?string $start = null;
    public ?string $end = null;

    public function exportExcel()
    {
        return Excel::download(
            new AccountingBalanceExport($this->start, $this->end),
            'balance-comptable.xlsx'
        );
    }

    public function exportPdf()
    {
        $company = CompanySetting::first();
        $rows = $this->rows();
        $totals = $this->totals($rows);

        $pdf = Pdf::loadView('exports.accounting-balance', compact('company', 'rows', 'totals'));

        return response()->streamDownload(fn () => print($pdf->output()), 'balance-comptable.pdf');
    }

    public function render()
    {
        $rows = $this->rows();
        $totals = $this->totals($rows);

        return view('livewire.accounting.balance-page', compact('rows', 'totals'))
            ->layout('layouts.app');
    }

    private function rows()
    {
        return $this->query()
            ->select([
                'accounts.id',
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

                $row->balance = $debit - $credit;

                return $row;
            });
    }

    private function totals($rows): array
    {
        return [
            'debit' => (float) $rows->sum('total_debit'),
            'credit' => (float) $rows->sum('total_credit'),
            'balance' => (float) $rows->sum('balance'),
        ];
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
