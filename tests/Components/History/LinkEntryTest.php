<?php

namespace Tests\Components\History;

use App\Models\Link;
use App\Models\LinkList;
use App\Models\Tag;
use App\Models\User;
use App\View\Components\History\LinkEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LinkEntryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->actingAs($user);
    }

    public function test_added_change(): void
    {
        $link = Link::factory()->create([
            'description' => null,
        ]);

        $link->update(['description' => 'Test Description']);

        $historyEntry = $link->audits()->first();

        $output = (new LinkEntry($historyEntry))->render();

        $this->assertStringContainsString('Added <code>Test Description</code> to Description', $output);
    }

    public function test_regular_change(): void
    {
        $link = Link::factory()->create([
            'description' => 'Test Description',
        ]);

        $link->description = 'New Description';
        $link->save();

        $historyEntry = $link->audits()->first();

        $output = (new LinkEntry($historyEntry))->render();

        $this->assertStringContainsString(
            'Changed Description from <code>Test Description</code> to <code>New Description</code>',
            $output
        );
    }

    public function test_remove_change(): void
    {
        $link = Link::factory()->create([
            'description' => 'Test Description',
        ]);

        $link->description = null;
        $link->save();

        $historyEntry = $link->audits()->first();

        $output = (new LinkEntry($historyEntry))->render();

        $this->assertStringContainsString('Removed <code>Test Description</code> from Description', $output);
    }

    public function test_model_deletion(): void
    {
        $link = Link::factory()->create();

        $link->delete();
        $this->travel(10)->seconds();
        $link->restore();

        $historyEntries = $link->audits()->get();

        $output = (new LinkEntry($historyEntries[0]))->render();
        $this->assertStringContainsString('Link was moved to the trash', $output);

        $output = (new LinkEntry($historyEntries[1]))->render();
        $this->assertStringContainsString('Link was restored', $output);
    }

    public function test_tags_added_change(): void
    {
        $link = Link::factory()->create();

        $this->post('links/1', [
            '_method' => 'patch',
            'link_id' => $link->id,
            'url' => $link->url,
            'title' => $link->title,
            'description' => $link->description,
            'lists' => null,
            'tags' => json_encode(['newtag']),
            'is_private' => $link->is_private,
        ]);

        $historyEntry = $link->audits()->first();

        $output = (new LinkEntry($historyEntry))->render();

        $this->assertStringContainsString('Added <code>newtag</code> to Tags', $output);
    }

    public function test_tags_change(): void
    {
        $startTag = Tag::factory()->create();
        $link = Link::factory()->create();

        $link->tags()->sync($startTag->id);

        $this->post('links/1', [
            '_method' => 'patch',
            'link_id' => $link->id,
            'url' => $link->url,
            'title' => $link->title,
            'description' => $link->description,
            'lists' => null,
            'tags' => json_encode(['newtag']),
            'is_private' => $link->is_private,
        ]);

        $historyEntry = $link->audits()->first();

        $output = (new LinkEntry($historyEntry))->render();

        $this->assertStringContainsString(
            sprintf('Changed Tags from <code>%s</code> to <code>newtag</code>', $startTag->name),
            $output
        );
    }

    public function test_tags_remove_change(): void
    {
        $startTag = Tag::factory()->create();
        $link = Link::factory()->create();

        $link->tags()->sync($startTag->id);

        $this->post('links/1', [
            '_method' => 'patch',
            'link_id' => $link->id,
            'url' => $link->url,
            'title' => $link->title,
            'description' => $link->description,
            'lists' => null,
            'tags' => null,
            'is_private' => $link->is_private,
        ]);

        $historyEntry = $link->audits()->first();

        $output = (new LinkEntry($historyEntry))->render();

        $this->assertStringContainsString(
            sprintf('Removed <code>%s</code> from Tags', $startTag->name),
            $output
        );
    }

    public function test_links_added_change(): void
    {
        $link = Link::factory()->create();

        $this->patch('links/1', [
            'link_id' => $link->id,
            'url' => $link->url,
            'title' => $link->title,
            'description' => $link->description,
            'lists' => json_encode(['New List', 'Example List']),
            'tags' => null,
            'is_private' => $link->is_private,
        ]);

        $historyEntry = $link->audits()->first();

        $output = (new LinkEntry($historyEntry))->render();

        $this->assertStringContainsString('Added <code>Example List, New List</code> to Lists', $output);
    }

    public function test_links_change(): void
    {
        $startList = LinkList::factory()->create();
        $link = Link::factory()->create();

        $link->lists()->sync($startList->id);

        $this->post('links/1', [
            '_method' => 'patch',
            'link_id' => $link->id,
            'url' => $link->url,
            'title' => $link->title,
            'description' => $link->description,
            'lists' => json_encode(['New List', 'Example List']),
            'tags' => null,
            'is_private' => $link->is_private,
        ]);

        $historyEntry = $link->audits()->first();

        $output = (new LinkEntry($historyEntry))->render();

        $this->assertStringContainsString(
            sprintf('Changed Lists from <code>%s</code> to <code>Example List, New List</code>', $startList->name),
            $output
        );
    }

    public function test_links_remove_change(): void
    {
        $startList = LinkList::factory()->create();
        $link = Link::factory()->create();

        $link->lists()->sync($startList->id);

        $this->post('links/1', [
            '_method' => 'patch',
            'link_id' => $link->id,
            'url' => $link->url,
            'title' => $link->title,
            'description' => $link->description,
            'lists' => null,
            'tags' => null,
            'is_private' => $link->is_private,
        ]);

        $historyEntry = $link->audits()->first();

        $output = (new LinkEntry($historyEntry))->render();

        $this->assertStringContainsString(
            sprintf('Removed <code>%s</code> from Lists', $startList->name),
            $output
        );
    }

    public function test_visibility_change(): void
    {
        $link = Link::factory()->create([
            'visibility' => 1,
        ]);

        $link->update(['visibility' => 2]);

        $historyEntry = $link->audits()->first();

        $output = (new LinkEntry($historyEntry))->render();

        $this->assertStringContainsString(
            'Changed Visibility Status from <code>Public</code> to <code>Internal</code>',
            $output
        );
    }
}
