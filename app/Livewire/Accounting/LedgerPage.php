<?php

namespace App\Livewire\Accounting;

use App\Exports\AccountingLedgerExport;
use App\Models\Account;
use App\Models\CompanySetting;
use App\Models\JournalEntryLine;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class LedgerPage extends Component
{
    use WithPagination;

    public ?string $start = null;
    public ?string $end = null;
    public ?int $account_id = null;
    public int $perPage = 25;

    public function updatingStart(): void
    {
        $this->resetPage();
    }

    public function updatingEnd(): void
    {
        $this->resetPage();
    }

    public function updatingAccountId(): void
    {
        $this->resetPage();
    }

    public function exportExcel()
    {
        return Excel::download(
            new AccountingLedgerExport($this->start, $this->end, $this->account_id),
            'grand-livre.xlsx'
        );
    }

    public function exportPdf()
    {
        $company = CompanySetting::first();
        $accounts = Account::query()->orderBy('number')->get();
        $selectedAccount = $accounts->firstWhere('id', $this->account_id);
        $openingBalance = $this->openingBalance();
        $lines = $this->query()
            ->with(['account', 'entry.journal'])
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->orderBy('journal_entries.entry_date')
            ->orderBy('journal_entry_lines.id')
            ->select('journal_entry_lines.*')
            ->get();

        $running = $openingBalance;
        $lines->transform(function (JournalEntryLine $line) use (&$running) {
            $running += (float) $line->debit - (float) $line->credit;
            $line->running_balance = $running;

            return $line;
        });

        $pdf = Pdf::loadView('exports.accounting-ledger', compact('company', 'lines', 'selectedAccount', 'openingBalance'));

        return response()->streamDownload(fn () => print($pdf->output()), 'grand-livre.pdf');
    }

    public function render()
    {
        $lines = $this->query()
            ->with(['account', 'entry.journal'])
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->orderBy('journal_entries.entry_date')
            ->orderBy('journal_entry_lines.id')
            ->select('journal_entry_lines.*')
            ->paginate($this->perPage);

        $accounts = Account::query()->orderBy('number')->get();
        $selectedAccount = $accounts->firstWhere('id', $this->account_id);
        $openingBalance = $this->openingBalance();

        $running = $openingBalance;
        $lines->getCollection()->transform(function (JournalEntryLine $line) use (&$running) {
            $running += (float) $line->debit - (float) $line->credit;
            $line->running_balance = $running;

            return $line;
        });

        return view('livewire.accounting.ledger-page', compact('lines', 'accounts', 'selectedAccount', 'openingBalance'))
            ->layout('layouts.app');
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
            })
            ->when($this->account_id, fn (Builder $query) => $query->where('account_id', $this->account_id));
    }

    private function openingBalance(): float
    {
        if (!$this->start) {
            return 0;
        }

        return (float) JournalEntryLine::query()
            ->whereHas('entry', fn (Builder $query) => $query->whereDate('entry_date', '<', $this->start))
            ->when($this->account_id, fn (Builder $query) => $query->where('account_id', $this->account_id))
            ->selectRaw('COALESCE(SUM(debit - credit), 0) as balance')
            ->value('balance');
    }
}
