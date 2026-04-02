<?php

namespace App\Listeners;

use App\Models\CompanySetting;
use App\Notifications\LoginAlertNotification;
use App\Services\NotificationService;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Notification;

class SendLoginAlert
{
    public function __construct(
        private NotificationService $notifications,
    ) {
    }

    public function handle(Login $event): void
    {
        if ($event->guard !== 'web') {
            return;
        }

        $settings = CompanySetting::query()->first();

        if (! $settings?->login_alert_enabled) {
            return;
        }

        $recipients = collect([
            $settings->login_alert_recipient,
            $settings->email,
        ])
            ->filter(fn (?string $email) => filled($email))
            ->map(fn (string $email) => trim($email))
            ->unique()
            ->values()
            ->all();

        if ($recipients === []) {
            return;
        }

        try {
            Notification::route('mail', $recipients)
                ->notify(new LoginAlertNotification(
                    user: $event->user,
                    ipAddress: request()->ip(),
                    userAgent: request()->userAgent(),
                    loggedInAt: now()->format('d/m/Y H:i:s'),
                ));

            $settings->forceFill([
                'login_alert_last_status' => 'success',
                'login_alert_last_error' => null,
                'login_alert_last_attempt_at' => now(),
            ])->save();

            $this->notifications->markManagersAsResolved('login-alert-mail-failed');
        } catch (\Throwable $exception) {
            report($exception);

            $settings->forceFill([
                'login_alert_last_status' => 'failed',
                'login_alert_last_error' => $exception->getMessage(),
                'login_alert_last_attempt_at' => now(),
            ])->save();

            $this->notifications->notifyManagers(
                'Echec envoi email alerte connexion',
                "Le mail d'alerte de connexion n'a pas été envoyé. Cause: {$exception->getMessage()}",
                'error',
                route('system.health'),
                'login-alert-mail-failed'
            );
        }
    }
}
