<?php

namespace Tests\Controller\App;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_dashboard_response(): void
    {
        $user = User::factory()->create(['name' => 'MrTestUser']);
        $this->actingAs($user);

        $response = $this->get('dashboard');

        $response->assertOk()
            ->assertSee('Hello MrTestUser!');
    }
}
