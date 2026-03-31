<?php

namespace Tests\Feature;

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
}
