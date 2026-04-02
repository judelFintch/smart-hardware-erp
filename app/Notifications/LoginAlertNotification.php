<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoginAlertNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $user,
        public ?string $ipAddress,
        public ?string $userAgent,
        public string $loggedInAt,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Alerte de connexion')
            ->greeting('Connexion détectée')
            ->line("Une connexion réussie au compte {$this->user->email} vient d'être détectée.")
            ->line("Utilisateur: {$this->user->name}")
            ->line("Rôle: {$this->user->role}")
            ->line("Date: {$this->loggedInAt}")
            ->line('Adresse IP: '.($this->ipAddress ?: 'Non disponible'))
            ->line('Navigateur/Appareil: '.($this->userAgent ?: 'Non disponible'))
            ->line("Si cette connexion n'est pas légitime, changez immédiatement le mot de passe du compte concerné.");
    }
}
