<?php

namespace Tests\Feature;

use App\Services\AccountingService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountingOperationsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_accounting_operations_page_displays_operation_mapping(): void
    {
        $user = User::factory()->create(['role' => 'owner']);
        $this->actingAs($user);

        app(AccountingService::class)->ensureDefaults();

        $this->get(route('accounting.operations'))
            ->assertOk()
            ->assertSee('Comptes appliques sur chaque operation du systeme')
            ->assertSee('Vente comptant')
            ->assertSee('Transfert de stock interne')
            ->assertSee('Paramétrage comptable', false);
    }
}
