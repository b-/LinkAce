<?php

namespace Tests\Controller\App;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTokenControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->actingAs($user);
    }

    public function test_token_overview(): void
    {
        $this->get('settings/api-tokens')->assertOk()->assertSee('API Tokens');
    }

    public function test_token_creation(): void
    {
        $this->post('settings/api-tokens', [
            'token_name' => 'invalid name',
        ])->assertSessionHasErrors(['token_name']);

        $this->post('settings/api-tokens', [
            'token_name' => 'validToken',
        ])
            ->assertRedirect('settings/api-tokens')
            ->assertSessionHas('new_token');

        $this->assertDatabaseHas('personal_access_tokens', [
            'name' => 'validToken',
            'tokenable_type' => User::class,
            'tokenable_id' => 1,
        ]);

        $this->post('settings/api-tokens', [
            'token_name' => 'validToken',
        ])->assertSessionHasErrors(['token_name']);
    }

    public function test_token_deletion(): void
    {
        $this->post('settings/api-tokens', [
            'token_name' => 'validToken',
        ]);

        $this->delete('settings/api-tokens/1')->assertRedirect('settings/api-tokens');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_token_deletion_for_foreign_token(): void
    {
        $user = User::factory()->create();
        $user->createToken('foreignToken');

        $this->delete('settings/api-tokens/1')->assertForbidden();
    }
}
