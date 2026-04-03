<?php

namespace App\Livewire\Accounting;

use App\Exports\AccountingIncomeStatementExport;
use App\Models\CompanySetting;
use App\Support\AccountingStatementBuilder;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class IncomeStatementPage extends Component
{
    public ?string $start = null;
    public ?string $end = null;

    public function exportExcel()
    {
        return Excel::download(
            new AccountingIncomeStatementExport($this->start, $this->end),
            'compte-de-resultat.xlsx'
        );
    }

    public function exportPdf()
    {
        $company = CompanySetting::first();
        $statement = app(AccountingStatementBuilder::class)->incomeStatement($this->start, $this->end);

        $pdf = Pdf::loadView('exports.accounting-income-statement', compact('company', 'statement'));

        return response()->streamDownload(fn () => print($pdf->output()), 'compte-de-resultat.pdf');
    }

    public function render()
    {
        $statement = app(AccountingStatementBuilder::class)->incomeStatement($this->start, $this->end);

        return view('livewire.accounting.income-statement-page', compact('statement'))
            ->layout('layouts.app');
    }
}
