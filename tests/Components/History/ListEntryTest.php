<?php

namespace Tests\Components\History;

use App\Models\LinkList;
use App\Models\User;
use App\View\Components\History\ListEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListEntryTest extends TestCase
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
        $list = LinkList::factory()->create([
            'description' => null,
        ]);

        $list->update(['description' => 'Test Description']);

        $historyEntry = $list->audits()->first();

        $output = (new ListEntry($historyEntry))->render();

        $this->assertStringContainsString('Added <code>Test Description</code> to List Description', $output);
    }

    public function test_regular_change(): void
    {
        $list = LinkList::factory()->create([
            'description' => 'Test Description',
        ]);

        $list->update(['description' => 'New Description']);

        $historyEntry = $list->audits()->first();

        $output = (new ListEntry($historyEntry))->render();

        $this->assertStringContainsString(
            'Changed List Description from <code>Test Description</code> to <code>New Description</code>',
            $output
        );
    }

    public function test_remove_change(): void
    {
        $list = LinkList::factory()->create([
            'description' => 'Test Description',
        ]);

        $list->update(['description' => null]);

        $historyEntry = $list->audits()->first();

        $output = (new ListEntry($historyEntry))->render();

        $this->assertStringContainsString('Removed <code>Test Description</code> from List Description', $output);
    }

    public function test_model_deletion(): void
    {
        $list = LinkList::factory()->create();

        $list->delete();
        $this->travel(10)->seconds();
        $list->restore();

        $historyEntries = $list->audits()->get();

        $output = (new ListEntry($historyEntries[0]))->render();
        $this->assertStringContainsString('List was moved to the trash', $output);

        $output = (new ListEntry($historyEntries[1]))->render();
        $this->assertStringContainsString('List was restored', $output);
    }
}
