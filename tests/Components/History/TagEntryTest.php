<?php

namespace Tests\Components\History;

use App\Models\LinkList;
use App\Models\Tag;
use App\Models\User;
use App\View\Components\History\ListEntry;
use App\View\Components\History\TagEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagEntryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->actingAs($user);
    }

    public function test_regular_change(): void
    {
        $tag = Tag::factory()->create([
            'name' => 'Test Tag',
        ]);

        $tag->update(['name' => 'New Tag']);

        $historyEntry = $tag->audits()->first();

        $output = (new TagEntry($historyEntry))->render();

        $this->assertStringContainsString(
            'Changed Tag Name from <code>Test Tag</code> to <code>New Tag</code>',
            $output
        );
    }

    public function test_model_deletion(): void
    {
        $tag = Tag::factory()->create();

        $tag->delete();
        $this->travel(10)->seconds();
        $tag->restore();

        $historyEntries = $tag->audits()->get();

        $output = (new TagEntry($historyEntries[0]))->render();
        $this->assertStringContainsString('Tag was moved to the trash', $output);

        $output = (new TagEntry($historyEntries[1]))->render();
        $this->assertStringContainsString('Tag was restored', $output);
    }
}
