<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_route_is_not_publicly_accessible(): void
    {
        $response = $this->get('/register');

        $response->assertNotFound();
    }
}
