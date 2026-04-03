<?php

namespace App\Exports;

use App\Models\JournalEntryLine;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AccountingLedgerExport implements FromCollection, WithHeadings
{
    public function __construct(
        private readonly ?string $start = null,
        private readonly ?string $end = null,
        private readonly ?int $accountId = null,
    ) {
    }

    public function headings(): array
    {
        return ['Date', 'Compte', 'Journal', 'Reference', 'Libelle', 'Debit', 'Credit', 'Solde'];
    }

    public function collection(): Collection
    {
        $running = $this->openingBalance();

        return $this->query()
            ->with(['account', 'entry.journal'])
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->orderBy('journal_entries.entry_date')
            ->orderBy('journal_entry_lines.id')
            ->select('journal_entry_lines.*')
            ->get()
            ->map(function (JournalEntryLine $line) use (&$running) {
                $running += (float) $line->debit - (float) $line->credit;

                return [
                    $line->entry?->entry_date?->format('Y-m-d'),
                    ($line->account?->number ?? '') . ' ' . ($line->account?->name ?? ''),
                    $line->entry?->journal?->code ?? '',
                    $line->entry?->reference ?? '',
                    $line->description ?: $line->entry?->description,
                    (float) $line->debit,
                    (float) $line->credit,
                    $running,
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
            })
            ->when($this->accountId, fn (Builder $query) => $query->where('account_id', $this->accountId));
    }

    private function openingBalance(): float
    {
        if (!$this->start) {
            return 0;
        }

        return (float) JournalEntryLine::query()
            ->whereHas('entry', fn (Builder $query) => $query->whereDate('entry_date', '<', $this->start))
            ->when($this->accountId, fn (Builder $query) => $query->where('account_id', $this->accountId))
            ->selectRaw('COALESCE(SUM(debit - credit), 0) as balance')
            ->value('balance');
    }
}
