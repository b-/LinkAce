<?php

namespace Tests\Controller\API;

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Controller\Traits\PreparesTestData;

class TagApiTest extends ApiTestCase
{
    use PreparesTestData;
    use RefreshDatabase;

    public function test_unauthorized_request(): void
    {
        $this->getJson('api/v2/tags')->assertUnauthorized();
    }

    public function test_index_request(): void
    {
        $this->createTestTags();

        $this->getJsonAuthorized('api/v2/tags')
            ->assertOk()
            ->assertJson([
                'data' => [
                    ['name' => 'Internal Tag'],
                    ['name' => 'Public Tag'],
                ],
            ])
            ->assertJsonMissing([
                'data' => [
                    ['name' => 'Private Tag'],
                ],
            ]);
    }

    public function test_minimal_create_request(): void
    {
        $this->postJsonAuthorized('api/v2/tags', [
            'name' => 'Test Tag',
        ])
            ->assertOk()
            ->assertJson([
                'name' => 'Test Tag',
            ]);

        $databaseTag = Tag::first();

        $this->assertEquals('Test Tag', $databaseTag->name);
    }

    public function test_full_create_request(): void
    {
        $this->postJsonAuthorized('api/v2/tags', [
            'name' => 'Test Tag',
            'is_private' => false,
        ])
            ->assertOk()
            ->assertJson([
                'name' => 'Test Tag',
            ]);

        $databaseTag = Tag::first();

        $this->assertEquals('Test Tag', $databaseTag->name);
    }

    public function test_invalid_create_request(): void
    {
        $this->postJsonAuthorized('api/v2/tags', [
            'name' => null,
            'visibility' => 'hello',
        ])->assertJsonValidationErrors([
            'name' => 'The name field is required.',
            'visibility' => 'The Visibility must bei either 1 (public), 2 (internal) or 3 (private).',
        ]);
    }

    public function test_show_request(): void
    {
        $this->createTestTags();

        $this->getJsonAuthorized('api/v2/tags/1')
            ->assertOk()
            ->assertJson([
                'name' => 'Public Tag',
            ]);

        $this->getJsonAuthorized('api/v2/tags/2')
            ->assertOk()
            ->assertJson([
                'name' => 'Internal Tag',
            ]);

        $this->getJsonAuthorized('api/v2/tags/3')
            ->assertForbidden();
    }

    public function test_show_request_not_found(): void
    {
        $this->getJsonAuthorized('api/v2/tags/1')->assertNotFound();
    }

    public function test_update_request(): void
    {
        $this->createTestTags();

        $this->patchJsonAuthorized('api/v2/tags/1', [
            'name' => 'Updated Public Tag',
            'visibility' => 1,
        ])
            ->assertOk()
            ->assertJson([
                'name' => 'Updated Public Tag',
            ]);

        $databaseList = Tag::find(1);

        $this->assertEquals('Updated Public Tag', $databaseList->name);

        // Test other tags
        $this->patchJsonAuthorized('api/v2/tags/2', [
            'name' => 'Updated Internal Tag',
            'visibility' => 1,
        ])
            ->assertOk()
            ->assertJson([
                'name' => 'Updated Internal Tag',
            ]);

        $this->patchJsonAuthorized('api/v2/tags/3', [
            'name' => 'Updated Private Tag',
            'visibility' => 1,
        ])
            ->assertForbidden();
    }

    public function test_invalid_update_request(): void
    {
        Tag::factory()->create();

        $this->patchJsonAuthorized('api/v2/tags/1', [
            'name' => null,
            'visibility' => 'hello',
        ])->assertJsonValidationErrors([
            'name' => 'The name field is required.',
            'visibility' => 'The Visibility must bei either 1 (public), 2 (internal) or 3 (private).',
        ]);
    }

    public function test_update_request_not_found(): void
    {
        $this->patchJsonAuthorized('api/v2/tags/1', [
            'name' => 'Updated Tag Title',
            'is_private' => false,
        ])->assertNotFound();
    }

    public function test_delete_request(): void
    {
        $this->createTestTags();

        $this->assertEquals(3, Tag::count());

        $this->deleteJsonAuthorized('api/v2/tags/1')->assertOk();
        $this->deleteJsonAuthorized('api/v2/tags/2')->assertForbidden();
        $this->deleteJsonAuthorized('api/v2/tags/3')->assertForbidden();

        $this->assertEquals(2, Tag::count());
    }

    public function test_delete_request_not_found(): void
    {
        $this->deleteJsonAuthorized('api/v2/tags/1')->assertNotFound();
    }
}
