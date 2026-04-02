<?php

namespace App\Listeners;

use App\Models\CompanySetting;
use App\Notifications\LoginAlertNotification;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Notification;

class SendLoginAlert
{
    public function handle(Login $event): void
    {
        if ($event->guard !== 'web') {
            return;
        }

        $settings = CompanySetting::query()->first();

        if (! $settings?->login_alert_enabled || ! $settings->login_alert_recipient) {
            return;
        }

        rescue(function () use ($event, $settings): void {
            Notification::route('mail', $settings->login_alert_recipient)
                ->notify(new LoginAlertNotification(
                    user: $event->user,
                    ipAddress: request()->ip(),
                    userAgent: request()->userAgent(),
                    loggedInAt: now()->format('d/m/Y H:i:s'),
                ));
        }, report: true);
    }
}
