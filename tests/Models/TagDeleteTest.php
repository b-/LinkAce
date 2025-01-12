<?php

namespace Tests\Models;

use App\Models\Tag;
use App\Models\User;
use App\Repositories\TagRepository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TagDeleteTest extends TestCase
{
    use DatabaseMigrations;
    use DatabaseTransactions;

    /** @var User */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_valid_category_creation(): void
    {
        $this->be($this->user);

        $tag = Tag::factory()->create();

        $deletionResult = TagRepository::delete($tag);

        $this->assertTrue($deletionResult);
    }
}
