<?php

namespace Tests\Feature;

use App\Models\CompanySetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemHealthTest extends TestCase
{
    use RefreshDatabase;

    public function test_system_health_page_displays_runtime_diagnostics(): void
    {
        $user = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($user)->get(route('system.health'));

        $response
            ->assertOk()
            ->assertSee('Session')
            ->assertSee('Journal applicatif')
            ->assertSee('PHP');
    }

    public function test_system_health_page_displays_last_login_alert_failure(): void
    {
        $user = User::factory()->create(['role' => 'owner']);

        CompanySetting::create([
            'name' => 'Entreprise',
            'currency' => 'CDF',
            'login_alert_enabled' => true,
            'login_alert_recipient' => 'alert@fintchweb.com',
            'login_alert_last_status' => 'failed',
            'login_alert_last_error' => 'SMTP authentication failed',
            'login_alert_last_attempt_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('system.health'));

        $response
            ->assertOk()
            ->assertSee('Dernier envoi échoué')
            ->assertSee('SMTP authentication failed')
            ->assertSee('alert@fintchweb.com');
    }
}
