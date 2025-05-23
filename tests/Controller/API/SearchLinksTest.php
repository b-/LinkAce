<?php

namespace Tests\Controller\API;

use App\Models\Link;
use App\Models\LinkList;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchLinksTest extends ApiTestCase
{
    use RefreshDatabase;

    public function test_unauthorized_request(): void
    {
        $this->getJson('api/v2/search/links')->assertUnauthorized();
    }

    public function test_without_query(): void
    {
        $msg = 'You must either enter a search query, or select a list, a tag or enable searching for broken links.';
        $this->getJsonAuthorized('api/v2/search/links')
            ->assertJsonValidationErrors([
            'query' => $msg,
            'only_lists' => $msg,
            'only_tags' => $msg,
            'broken_only' => $msg,
        ]);
    }

    public function test_regular_search_result(): void
    {
        $link = Link::factory()->create([
            'user_id' => $this->user->id,
            'url' => 'https://example.com',
        ]);

        $link2 = Link::factory()->create([
            'user_id' => $this->user->id,
            'url' => 'https://another-example.org',
        ]);

        // This link must not be present in the results
        $excludedLink = Link::factory()->create([
            'user_id' => $this->user->id,
            'url' => 'https://test.com',
        ]);

        $url = sprintf('api/v2/search/links?query=%s', 'example');
        $this->getJsonAuthorized($url)
            ->assertOk()
            ->assertJsonFragment([
                'current_page' => 1,
            ])
            ->assertJsonFragment([
                'url' => $link->url,
            ])
            ->assertJsonFragment([
                'url' => $link2->url,
            ])
            ->assertJsonMissing([
                'url' => $excludedLink->url,
            ]);
    }

    public function test_search_by_title(): void
    {
        $link = Link::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Test Title',
        ]);

        // This link must not be present in the results
        $excludedLink = Link::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Nobody cares',
        ]);

        $url = sprintf('api/v2/search/links?query=%s&search_title=1', 'Test');
        $this->getJsonAuthorized($url)
            ->assertOk()
            ->assertJsonFragment([
                'url' => $link->url,
            ])
            ->assertJsonMissing([
                'url' => $excludedLink->url,
            ]);
    }

    public function test_search_by_description(): void
    {
        $link = Link::factory()->create([
            'user_id' => $this->user->id,
            'url' => 'https://test.com',
            'description' => 'Example description',
        ]);

        // This link must not be present in the results
        $excludedLink = Link::factory()->create([
            'user_id' => $this->user->id,
            'url' => 'https://test.org',
            'description' => 'Lorem Ipsum',
        ]);

        $url = sprintf('api/v2/search/links?query=%s&search_description=1', 'Example');
        $this->getJsonAuthorized($url)
            ->assertOk()
            ->assertJsonFragment([
                'url' => $link->url,
            ])
            ->assertJsonMissing([
                'url' => $excludedLink->url,
            ]);
    }

    public function test_search_private_only(): void
    {
        $link = Link::factory()->create([
            'user_id' => $this->user->id,
            'url' => 'https://test.com',
            'visibility' => 1,
        ]);

        // This link must not be present in the results
        $excludedLink = Link::factory()->create([
            'user_id' => $this->user->id,
            'url' => 'https://test.org',
            'visibility' => 3,
        ]);

        $url = sprintf('api/v2/search/links?query=%s&visibility=1', 'test');
        $this->getJsonAuthorized($url)
            ->assertOk()
            ->assertJsonFragment([
                'url' => $link->url,
            ])
            ->assertJsonMissing([
                'url' => $excludedLink->url,
            ]);
    }

    public function test_search_broken_only(): void
    {
        $link = Link::factory()->create([
            'user_id' => $this->user->id,
            'url' => 'https://test.com',
            'status' => Link::STATUS_BROKEN,
        ]);

        // This link must not be present in the results
        $excludedLink = Link::factory()->create([
            'user_id' => $this->user->id,
            'url' => 'https://test.org',
        ]);

        $url = sprintf('api/v2/search/links?query=%s&broken_only=1', 'test');
        $this->getJsonAuthorized($url)
            ->assertOk()
            ->assertJsonFragment([
                'url' => $link->url,
            ])
            ->assertJsonMissing([
                'url' => $excludedLink->url,
            ]);
    }

    public function test_search_with_lists(): void
    {
        $list = LinkList::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Scientific Articles',
        ]);

        $link = Link::factory()->create([
            'user_id' => $this->user->id,
            'url' => 'https://test.com',
        ]);

        $link->lists()->sync([$list->id]);

        // This link must not be present in the results
        $excludedLink = Link::factory()->create([
            'user_id' => $this->user->id,
            'url' => 'https://test.org',
        ]);

        $url = sprintf('api/v2/search/links?only_lists=%s', $list->id);
        $this->getJsonAuthorized($url)
            ->assertOk()
            ->assertJsonFragment([
                'url' => $link->url,
            ])
            ->assertJsonMissing([
                'url' => $excludedLink->url,
            ]);
    }

    public function test_search_with_tags(): void
    {
        $tag = Tag::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'artificial-intelligence',
        ]);

        $link = Link::factory()->create([
            'user_id' => $this->user->id,
            'url' => 'https://test.com',
        ]);

        $link->tags()->sync([$tag->id]);

        // This link must not be present in the results
        $excludedLink = Link::factory()->create([
            'user_id' => $this->user->id,
            'url' => 'https://test.org',
        ]);

        $url = sprintf('api/v2/search/links?only_tags=%s', $tag->id);
        $this->getJsonAuthorized($url)
            ->assertOk()
            ->assertJsonFragment([
                'url' => $link->url,
            ])
            ->assertJsonMissing([
                'url' => $excludedLink->url,
            ]);
    }
}
