<?php

namespace Tests\Feature;

use App\Livewire\System\Backups;
use App\Models\CompanySetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemBackupsTest extends TestCase
{
    use RefreshDatabase;

    public function test_backups_page_is_displayed_for_manager_profiles(): void
    {
        $user = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($user)->get(route('system.backups'));

        $response
            ->assertOk()
            ->assertSee('Snapshot applicatif exportable');
    }

    public function test_snapshot_download_contains_company_data(): void
    {
        $user = User::factory()->create(['role' => 'owner']);

        CompanySetting::query()->create([
            'name' => 'Quin Brandy',
            'currency' => 'CDF',
        ]);

        $this->actingAs($user);

        $response = app(Backups::class)->downloadSnapshot();

        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertStringContainsString('"company"', $content);
        $this->assertStringContainsString('Quin Brandy', $content);
        $this->assertStringContainsString('"notifications"', $content);
    }
}
