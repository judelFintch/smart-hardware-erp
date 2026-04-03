<?php

namespace App\Livewire\Accounting;

use App\Exports\AccountingBalanceSheetExport;
use App\Models\CompanySetting;
use App\Support\AccountingStatementBuilder;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class BalanceSheetPage extends Component
{
    public ?string $start = null;
    public ?string $end = null;

    public function exportExcel()
    {
        return Excel::download(
            new AccountingBalanceSheetExport($this->start, $this->end),
            'bilan-simplifie.xlsx'
        );
    }

    public function exportPdf()
    {
        $company = CompanySetting::first();
        $statement = app(AccountingStatementBuilder::class)->balanceSheet($this->start, $this->end);

        $pdf = Pdf::loadView('exports.accounting-balance-sheet', compact('company', 'statement'));

        return response()->streamDownload(fn () => print($pdf->output()), 'bilan-simplifie.pdf');
    }

    public function render()
    {
        $statement = app(AccountingStatementBuilder::class)->balanceSheet($this->start, $this->end);

        return view('livewire.accounting.balance-sheet-page', compact('statement'))
            ->layout('layouts.app');
    }
}
