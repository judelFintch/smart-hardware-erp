<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationService
{
    public function notifyUsers(iterable $users, string $title, ?string $message = null, string $level = 'info', ?string $link = null, ?string $fingerprint = null): void
    {
        $users = $users instanceof Collection ? $users : collect($users);

        foreach ($users as $user) {
            if (!$user instanceof User) {
                continue;
            }

            if ($fingerprint) {
                $exists = AppNotification::query()
                    ->where('user_id', $user->id)
                    ->where('fingerprint', $fingerprint)
                    ->whereNull('read_at')
                    ->exists();

                if ($exists) {
                    continue;
                }
            }

            AppNotification::create([
                'user_id' => $user->id,
                'level' => $level,
                'title' => $title,
                'message' => $message,
                'link' => $link,
                'fingerprint' => $fingerprint,
            ]);
        }
    }

    public function markAsResolved(?int $userId, string $fingerprint): void
    {
        AppNotification::query()
            ->when($userId, fn ($query) => $query->where('user_id', $userId), fn ($query) => $query->whereNull('user_id'))
            ->where('fingerprint', $fingerprint)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function markManagersAsResolved(string $fingerprint): void
    {
        $userIds = User::query()
            ->whereIn('role', ['owner', 'manager'])
            ->pluck('id');

        foreach ($userIds as $userId) {
            $this->markAsResolved((int) $userId, $fingerprint);
        }
    }

    public function notifyManagers(string $title, ?string $message = null, string $level = 'info', ?string $link = null, ?string $fingerprint = null): void
    {
        $users = User::query()
            ->whereIn('role', ['owner', 'manager'])
            ->get();

        $this->notifyUsers($users, $title, $message, $level, $link, $fingerprint);
    }
}
