<?php

namespace Tests\Feature\Auth;

use App\Listeners\SendLoginAlert;
use App\Models\AppNotification;
use App\Models\CompanySetting;
use App\Models\User;
use App\Notifications\LoginAlertNotification;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Support\Facades\Notification;
use Livewire\Volt\Volt;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.login');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create(['role' => 'owner']);

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('sales.index', absolute: false));

        $this->assertAuthenticated();
    }

    public function test_login_alert_is_sent_when_enabled(): void
    {
        Notification::fake();

        CompanySetting::create([
            'name' => 'Entreprise',
            'currency' => 'CDF',
            'email' => 'contact@fintchweb.com',
            'login_alert_enabled' => true,
            'login_alert_recipient' => 'alert@fintchweb.com',
        ]);

        $user = User::factory()->create();

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password');

        $component->call('login');

        $settings = CompanySetting::query()->first();

        Notification::assertSentOnDemand(LoginAlertNotification::class, function ($notification, $channels, $notifiable) use ($user) {
            return in_array('mail', $channels, true)
                && $notifiable->routes['mail'] === ['alert@fintchweb.com', 'contact@fintchweb.com']
                && $notification->user->is($user);
        });

        $this->assertSame('success', $settings?->fresh()->login_alert_last_status);
        $this->assertNull($settings?->fresh()->login_alert_last_error);
        $this->assertNotNull($settings?->fresh()->login_alert_last_attempt_at);
    }

    public function test_login_alert_failure_is_recorded_and_notified_in_app(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $manager = User::factory()->create(['role' => 'manager']);
        $user = User::factory()->create(['role' => 'seller']);

        $settings = CompanySetting::create([
            'name' => 'Entreprise',
            'currency' => 'CDF',
            'email' => 'contact@fintchweb.com',
            'login_alert_enabled' => true,
            'login_alert_recipient' => 'alert@fintchweb.com',
        ]);

        $dispatcher = \Mockery::mock(Dispatcher::class);
        $dispatcher->shouldReceive('send')
            ->once()
            ->andThrow(new \RuntimeException('SMTP authentication failed'));

        $this->app->instance(Dispatcher::class, $dispatcher);

        app(SendLoginAlert::class)->handle(new Login('web', $user, false));

        $settings->refresh();

        $this->assertSame('failed', $settings->login_alert_last_status);
        $this->assertSame('SMTP authentication failed', $settings->login_alert_last_error);
        $this->assertNotNull($settings->login_alert_last_attempt_at);

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $owner->id,
            'level' => 'error',
            'title' => 'Echec envoi email alerte connexion',
        ]);

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $manager->id,
            'level' => 'error',
            'title' => 'Echec envoi email alerte connexion',
        ]);

        $this->assertSame(2, AppNotification::query()->where('fingerprint', 'login-alert-mail-failed')->count());
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'wrong-password');

        $component->call('login');

        $component
            ->assertHasErrors()
            ->assertNoRedirect();

        $this->assertGuest();
    }

    public function test_login_alert_uses_company_email_when_no_dedicated_alert_email_is_configured(): void
    {
        Notification::fake();

        CompanySetting::create([
            'name' => 'Entreprise',
            'currency' => 'CDF',
            'email' => 'contact@fintchweb.com',
            'login_alert_enabled' => true,
            'login_alert_recipient' => null,
        ]);

        $user = User::factory()->create();

        Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->call('login');

        Notification::assertSentOnDemand(LoginAlertNotification::class, function ($notification, $channels, $notifiable) use ($user) {
            return in_array('mail', $channels, true)
                && $notifiable->routes['mail'] === ['contact@fintchweb.com']
                && $notification->user->is($user);
        });
    }

    public function test_navigation_menu_can_be_rendered(): void
    {
        $user = User::factory()->create(['role' => 'owner']);

        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response
            ->assertOk()
            ->assertSeeVolt('layout.navigation');
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Volt::test('layout.navigation');

        $component->call('logout');

        $component
            ->assertHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
