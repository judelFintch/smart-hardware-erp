<?php

namespace App\Livewire\Accounting;

use App\Models\JournalEntry;
use Livewire\Component;
use Livewire\WithPagination;

class EntriesIndex extends Component
{
    use WithPagination;

    public int $perPage = 20;

    public function render()
    {
        $entries = JournalEntry::query()
            ->with(['journal', 'user', 'lines.account'])
            ->latest('entry_date')
            ->latest('id')
            ->paginate($this->perPage);

        return view('livewire.accounting.entries-index', compact('entries'))
            ->layout('layouts.app');
    }
}
