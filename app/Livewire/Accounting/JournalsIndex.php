<?php

namespace App\Livewire\Accounting;

use App\Models\Journal;
use Livewire\Component;

class JournalsIndex extends Component
{
    public function render()
    {
        $journals = Journal::query()->orderBy('code')->get();

        return view('livewire.accounting.journals-index', compact('journals'))
            ->layout('layouts.app');
    }
}
