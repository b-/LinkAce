<?php

namespace Tests\Controller\API;

use App\Models\LinkList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Controller\Traits\PreparesTestData;

class ListApiTest extends ApiTestCase
{
    use RefreshDatabase;
    use PreparesTestData;

    public function test_unauthorized_request(): void
    {
        $this->getJson('api/v2/lists')->assertUnauthorized();
    }

    public function test_index_request(): void
    {
        $this->createTestLists();

        $this->getJsonAuthorized('api/v2/lists')
            ->assertOk()
            ->assertJson([
                'data' => [
                    ['name' => 'Internal List'],
                    ['name' => 'Public List'],
                ],
            ])->assertJsonMissing([
                'data' => [
                    ['name' => 'Private List'],
                ],
            ]);
    }

    public function test_minimal_create_request(): void
    {
        $this->postJsonAuthorized('api/v2/lists', [
            'name' => 'Test List',
        ])
            ->assertOk()
            ->assertJson([
                'name' => 'Test List',
            ]);

        $databaseList = LinkList::first();

        $this->assertEquals('Test List', $databaseList->name);
    }

    public function test_full_create_request(): void
    {
        $this->postJsonAuthorized('api/v2/lists', [
            'name' => 'Test List',
            'description' => 'There could be a description here',
            'is_private' => false,
            'check_disabled' => false,
        ])
            ->assertOk()
            ->assertJson([
                'name' => 'Test List',
            ]);

        $databaseList = LinkList::first();

        $this->assertEquals('Test List', $databaseList->name);
    }

    public function test_invalid_create_request(): void
    {
        $this->postJsonAuthorized('api/v2/lists', [
            'name' => null,
            'description' => ['bla'],
            'visibility' => 'hello',
        ])->assertJsonValidationErrors([
            'name' => 'The name field is required.',
            'description' => 'The description must be a string.',
            'visibility' => 'The Visibility must bei either 1 (public), 2 (internal) or 3 (private).',
        ]);
    }

    public function test_show_request(): void
    {
        $this->createTestLists();

        $expectedLinkApiUrl = 'http://localhost/api/v2/lists/1/links';

        $this->getJsonAuthorized('api/v2/lists/1')
            ->assertOk()
            ->assertJson([
                'name' => 'Public List',
                'links' => $expectedLinkApiUrl,
            ]);

        $this->getJsonAuthorized('api/v2/lists/2')->assertJson(['name' => 'Internal List']);
        $this->getJsonAuthorized('api/v2/lists/3')->assertForbidden();
    }

    public function test_show_request_not_found(): void
    {
        $this->getJsonAuthorized('api/v2/lists/1')->assertNotFound();
    }

    public function test_update_request(): void
    {
        $this->createTestLists();

        $this->patchJsonAuthorized('api/v2/lists/1', [
            'name' => 'Updated Public List',
            'description' => 'Custom Description',
            'visibility' => 1,
        ])
            ->assertOk()
            ->assertJson([
                'name' => 'Updated Public List',
            ]);

        $databaseList = LinkList::find(1);
        $this->assertEquals('Updated Public List', $databaseList->name);

        // Test other lists
        $this->patchJsonAuthorized('api/v2/lists/2', [
            'name' => 'Updated Internal List',
            'description' => 'Custom Description',
            'visibility' => 1,
        ])
            ->assertOk()
            ->assertJson([
                'name' => 'Updated Internal List',
            ]);

        $this->patchJsonAuthorized('api/v2/lists/3', [
            'name' => 'Updated Internal List',
            'description' => 'Custom Description',
            'visibility' => 1,
        ])->assertForbidden();
    }

    public function test_invalid_update_request(): void
    {
        LinkList::factory()->create();

        $this->patchJsonAuthorized('api/v2/lists/1', [
            'name' => null,
            'description' => ['bla'],
            'visibility' => 'hello',
        ])->assertJsonValidationErrors([
            'name' => 'The name field is required.',
            'description' => 'The description must be a string.',
            'visibility' => 'The Visibility must bei either 1 (public), 2 (internal) or 3 (private).',
        ]);
    }

    public function test_update_request_not_found(): void
    {
        $this->patchJsonAuthorized('api/v2/lists/1', [
            'name' => 'Updated List Title',
            'description' => 'Custom Description',
            'is_private' => false,
        ])->assertNotFound();
    }

    public function test_delete_request(): void
    {
        $this->createTestLists();

        $this->assertEquals(3, LinkList::count());

        $this->deleteJsonAuthorized('api/v2/lists/1')->assertOk();
        $this->deleteJsonAuthorized('api/v2/lists/2')->assertForbidden();
        $this->deleteJsonAuthorized('api/v2/lists/3')->assertForbidden();

        $this->assertEquals(2, LinkList::count());
    }

    public function test_delete_request_not_found(): void
    {
        $this->deleteJsonAuthorized('api/v2/lists/1')->assertNotFound();
    }
}
