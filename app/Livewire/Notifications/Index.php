<?php

namespace App\Livewire\Notifications;

use App\Models\AppNotification;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public int $perPage = 20;

    public function markAsRead(int $notificationId): void
    {
        AppNotification::query()
            ->where('user_id', auth()->id())
            ->whereKey($notificationId)
            ->update(['read_at' => now()]);
    }

    public function markAllAsRead(): void
    {
        AppNotification::query()
            ->where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function render()
    {
        $notifications = AppNotification::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.notifications.index', compact('notifications'))
            ->layout('layouts.app');
    }
}
