<?php

namespace Tests\Controller\Guest;

use App\Models\LinkList;
use App\Models\User;
use App\Settings\SystemSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        SystemSettings::fake([
            'guest_access_enabled' => true,
            'setup_completed' => true,
        ]);
    }

    public function test_valid_list_overview_response(): void
    {
        User::factory()->create();

        LinkList::factory()->create([
            'name' => 'public list',
            'visibility' => 1,
        ]);
        LinkList::factory()->create([
            'name' => 'private list',
            'visibility' => 3,
        ]);

        $response = $this->get('guest/lists');

        $response->assertOk()
            ->assertSee('public list')
            ->assertDontSee('private list');
    }

    public function test_valid_list_detail_response(): void
    {
        User::factory()->create();

        LinkList::factory()->create([
            'name' => 'test list name',
            'visibility' => 1,
        ]);

        $response = $this->get('guest/lists/1');

        $response->assertOk()->assertSee('test list name');
    }

    public function test_invalid_list_detail_response(): void
    {
        User::factory()->create();

        LinkList::factory()->create([
            'name' => 'test list name',
            'visibility' => 3,
        ]);

        $this->get('guest/lists/1')->assertNotFound();
        $this->get('guest/lists/mylist')->assertNotFound();
    }
}
