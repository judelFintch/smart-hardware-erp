<?php

namespace Tests\Feature;

use App\Livewire\Notifications\Index as NotificationsIndex;
use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_notifications_page_is_displayed_for_manager_profiles(): void
    {
        $user = User::factory()->create(['role' => 'owner']);

        AppNotification::query()->create([
            'user_id' => $user->id,
            'level' => 'warning',
            'title' => 'Stock bas detecte',
            'message' => 'Article critique.',
        ]);

        $response = $this->actingAs($user)->get(route('notifications.index'));

        $response
            ->assertOk()
            ->assertSee('Stock bas detecte');
    }

    public function test_notification_can_be_marked_as_read(): void
    {
        $user = User::factory()->create(['role' => 'owner']);
        $notification = AppNotification::query()->create([
            'user_id' => $user->id,
            'level' => 'info',
            'title' => 'Paramètres mis à jour',
        ]);

        $this->actingAs($user);

        Livewire::test(NotificationsIndex::class)
            ->call('markAsRead', $notification->id);

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_all_notifications_can_be_marked_as_read(): void
    {
        $user = User::factory()->create(['role' => 'owner']);

        AppNotification::query()->create([
            'user_id' => $user->id,
            'level' => 'info',
            'title' => 'Notification 1',
        ]);

        AppNotification::query()->create([
            'user_id' => $user->id,
            'level' => 'warning',
            'title' => 'Notification 2',
        ]);

        $this->actingAs($user);

        Livewire::test(NotificationsIndex::class)
            ->call('markAllAsRead');

        $this->assertSame(0, AppNotification::query()->where('user_id', $user->id)->whereNull('read_at')->count());
    }
}
