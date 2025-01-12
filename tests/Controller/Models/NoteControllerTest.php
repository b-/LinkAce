<?php

namespace Tests\Controller\Models;

use App\Models\Link;
use App\Models\Note;
use App\Models\User;
use App\Settings\UserSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Controller\Traits\PreparesTestData;
use Tests\TestCase;

class NoteControllerTest extends TestCase
{
    use RefreshDatabase;
    use PreparesTestData;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->actingAs($this->user);
    }

    public function test_minimal_store_request(): void
    {
        $this->createTestLinks();

        $this->post('notes', [
            'link_id' => 1,
            'note' => 'Some public test note',
            'visibility' => 1,
        ])->assertRedirect('links/1');

        $this->get('links/1')->assertSee('Some public test note');

        $this->post('notes', [
            'link_id' => 2,
            'note' => 'Some internal test note',
            'visibility' => 1,
        ])->assertRedirect('links/2');

        $this->get('links/2')->assertSee('Some internal test note');

        $this->post('notes', [
            'link_id' => 3,
            'note' => 'Some private test note',
            'visibility' => 1,
        ])->assertForbidden();
    }

    public function test_internal_store_request(): void
    {
        $link = Link::factory()->create();

        $response = $this->post('notes', [
            'link_id' => $link->id,
            'note' => 'Some internal test Note',
            'visibility' => 2,
        ]);

        $response->assertRedirect('links/1');

        $this->get('links/1')
            ->assertSee('Some internal test Note')
            ->assertSee('Internal Note');
    }

    public function test_private_store_request(): void
    {
        $link = Link::factory()->create();

        $response = $this->post('notes', [
            'link_id' => $link->id,
            'note' => 'Some private test Note',
            'visibility' => 3,
        ]);

        $response->assertRedirect('links/1');

        $this->get('links/1')
            ->assertSee('Some private test Note')
            ->assertSee('Private Note');
    }

    public function test_store_request_with_markdown(): void
    {
        UserSettings::fake([
            'markdown_for_text' => true,
        ]);

        $link = Link::factory()->create();

        $this->post('notes', [
            'link_id' => $link->id,
            'note' => 'Lorem _ipsum dolor_',
            'visibility' => 1,
        ])->assertRedirect('links/1');

        $this->get('links/1')->assertSee('Lorem <em>ipsum dolor</em>', false);
    }

    public function test_validation_error_for_create(): void
    {
        $link = Link::factory()->create();

        $this->post('notes', [
            'link_id' => $link->id,
            'note' => null,
            'visibility' => 1,
        ])->assertSessionHasErrors(['note']);
    }

    public function test_store_request_for_foreign_private_link(): void
    {
        $this->createTestLinks();

        $this->post('notes', [
            'link_id' => 3,
            'note' => 'Lorem ipsum dolor',
            'visibility' => 1,
        ])->assertForbidden();
    }

    public function test_store_request_for_missing_link(): void
    {
        $this->post('notes', [
            'link_id' => '1',
            'note' => 'Lorem ipsum dolor',
            'visibility' => 1,
        ])->assertForbidden();
    }

    public function test_edit_view(): void
    {
        $this->createTestNotes();

        $this->get('notes/1/edit')
            ->assertOk()
            ->assertSee('Edit Note');
    }

    public function test_invalid_edit_request(): void
    {
        $this->get('notes/1/edit')->assertNotFound();
    }

    public function test_update_response(): void
    {
        $note = Note::factory()->create();

        $this->patch('notes/1', [
            'link_id' => 1,
            'note' => 'Lorem ipsum dolor est updated',
            'visibility' => 1,
        ])->assertRedirect('links/1');

        $this->assertEquals('Lorem ipsum dolor est updated', $note->refresh()->note);
    }

    public function test_missing_model_error_for_update(): void
    {
        $this->patch('notes/1', [
            'link_id' => 1,
            'note' => 'Lorem ipsum dolor est updated',
            'visibility' => 1,
        ])->assertNotFound();
    }

    public function test_validation_error_for_update(): void
    {
        $this->createTestNotes();

        $this->patch('notes/1', [
            //'note' => 'Lorem ipsum dolor est updated',
            'visibility' => 1,
        ])->assertSessionHasErrors(['note']);
    }

    public function test_delete_response(): void
    {
        $this->createTestNotes();

        $this->assertEquals(3, Note::count());

        $this->deleteJson('notes/1')->assertRedirect('links/1');
        $this->deleteJson('notes/2')->assertForbidden();
        $this->deleteJson('notes/3')->assertForbidden();

        $this->assertEquals(2, Note::count());
    }

    public function test_missing_model_error_for_delete(): void
    {
        $this->deleteJson('notes/1')->assertNotFound();
    }
}
