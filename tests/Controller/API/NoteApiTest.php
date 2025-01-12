<?php

namespace Tests\Controller\API;

use App\Models\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Controller\Traits\PreparesTestData;

class NoteApiTest extends ApiTestCase
{
    use PreparesTestData;
    use RefreshDatabase;

    public function test_minimal_create_request(): void
    {
        $this->createTestLinks();

        $this->postJsonAuthorized('api/v2/notes', [
            'link_id' => 1,
            'note' => 'Quae vero auctorem tractata ab fiducia dicuntur.',
            'visibility' => 1,
        ])
            ->assertOk()
            ->assertJson([
                'note' => 'Quae vero auctorem tractata ab fiducia dicuntur.',
                'visibility' => 1,
            ]);

        $databaseNote = Note::first();
        $this->assertEquals('Quae vero auctorem tractata ab fiducia dicuntur.', $databaseNote->note);

        // Test notes for other links
        $this->postJsonAuthorized('api/v2/notes', [
            'link_id' => 2,
            'note' => 'Quae vero auctorem tractata ab fiducia dicuntur.',
            'visibility' => 1,
        ])
            ->assertOk()
            ->assertJson([
                'note' => 'Quae vero auctorem tractata ab fiducia dicuntur.',
                'visibility' => 1,
            ]);

        $this->postJsonAuthorized('api/v2/notes', [
            'link_id' => 3,
            'note' => 'Quae vero auctorem tractata ab fiducia dicuntur.',
            'visibility' => 1,
        ])->assertForbidden();
    }

    public function test_invalid_create_request(): void
    {
        $this->postJsonAuthorized('api/v2/notes', [
            'link_id' => null,
            'note' => null,
        ])->assertForbidden(); // A valid link cannot be determined, thus it's unauthorized
    }

    public function test_update_request(): void
    {
        $this->createTestLinks();
        Note::factory()->create(['link_id' => 1]);
        Note::factory()->create(['link_id' => 2]); // Note for internal link of other user
        Note::factory()->create(['link_id' => 3]); // Note for private link of other user

        $this->patchJsonAuthorized('api/v2/notes/1', [
            'note' => 'Gallia est omnis divisa in partes tres, quarum.',
            'visibility' => 1,
        ])
            ->assertOk()
            ->assertJson([
                'note' => 'Gallia est omnis divisa in partes tres, quarum.',
            ]);

        $databaseNote = Note::first();
        $this->assertEquals('Gallia est omnis divisa in partes tres, quarum.', $databaseNote->note);

        // Test for other links
        $this->patchJsonAuthorized('api/v2/notes/2', [
            'note' => 'Gallia est omnis divisa in partes tres, quarum.',
            'visibility' => 1,
        ])
            ->assertOk()
            ->assertJson([
                'note' => 'Gallia est omnis divisa in partes tres, quarum.',
            ]);

        $this->patchJsonAuthorized('api/v2/notes/3', [
            'note' => 'Gallia est omnis divisa in partes tres, quarum.',
            'visibility' => 1,
        ])->assertForbidden();
    }

    public function test_invalid_update_request(): void
    {
        Note::factory()->create();

        $this->patchJsonAuthorized('api/v2/notes/1', [
            //
        ])->assertJsonValidationErrors([
            'note' => 'The note field is required.',
        ]);
    }

    public function test_update_request_not_found(): void
    {
        $this->patchJsonAuthorized('api/v2/notes/1', [
            'note' => 'Sed haec quis possit intrepidus aestimare tellus.',
            'is_private' => false,
        ])->assertNotFound();
    }

    public function test_delete_request(): void
    {
        $this->createTestNotes();

        $this->assertEquals(3, Note::count());

        $this->deleteJsonAuthorized('api/v2/notes/1')->assertOk();
        $this->deleteJsonAuthorized('api/v2/notes/2')->assertForbidden();
        $this->deleteJsonAuthorized('api/v2/notes/3')->assertForbidden();

        $this->assertEquals(2, Note::count());
    }

    public function test_delete_request_not_found(): void
    {
        $this->deleteJsonAuthorized('api/v2/notes/1')->assertNotFound();
    }
}
