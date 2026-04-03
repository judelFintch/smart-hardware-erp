<?php

namespace App\Livewire\Accounting;

use App\Models\Journal;
use App\Models\JournalEntry;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class JournalBookPage extends Component
{
    use WithPagination;

    public ?string $start = null;
    public ?string $end = null;
    public ?int $journal_id = null;
    public int $perPage = 20;

    public function updatingStart(): void
    {
        $this->resetPage();
    }

    public function updatingEnd(): void
    {
        $this->resetPage();
    }

    public function updatingJournalId(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $entries = JournalEntry::query()
            ->with(['journal', 'user', 'lines.account'])
            ->when($this->start, fn (Builder $query) => $query->whereDate('entry_date', '>=', $this->start))
            ->when($this->end, fn (Builder $query) => $query->whereDate('entry_date', '<=', $this->end))
            ->when($this->journal_id, fn (Builder $query) => $query->where('journal_id', $this->journal_id))
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->paginate($this->perPage);

        $journals = Journal::query()->orderBy('code')->get();

        return view('livewire.accounting.journal-book-page', compact('entries', 'journals'))
            ->layout('layouts.app');
    }
}
