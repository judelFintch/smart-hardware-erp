<?php

namespace App\Livewire\ActivityLogs;

use App\Models\ActivityLog;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public int $perPage = 20;

    public function render()
    {
        $logs = ActivityLog::with('user')->orderByDesc('id')->paginate($this->perPage);

        return view('livewire.activity-logs.index', compact('logs'))
            ->layout('layouts.app');
    }
}
