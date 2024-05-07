<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SortArticlesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_sort_articles_by_title(): void
    {
        Article::factory()->create([
            'title' => 'C Title',
        ]);

        Article::factory()->create([
            'title' => 'A Title',
        ]);

        Article::factory()->create([
            'title' => 'B Title',
        ]);

        $url = route('api.v1.articles.index', ['sort' => 'title']);

        $this->getJson($url)
            ->assertSeeInOrder(['A Title', 'B Title', 'C Title']);
    }

    /** @test */
    public function can_sort_articles_by_title_descending(): void
    {
        Article::factory()->create([
            'title' => 'C Title',
        ]);

        Article::factory()->create([
            'title' => 'A Title',
        ]);

        Article::factory()->create([
            'title' => 'B Title',
        ]);

        $url = route('api.v1.articles.index', ['sort' => '-title']);

        $this->getJson($url)
            ->assertSeeInOrder(['C Title', 'B Title', 'A Title']);
    }

    /** @test */
    public function can_sort_articles_by_content(): void
    {
        Article::factory()->create([
            'content' => 'C content',
        ]);

        Article::factory()->create([
            'content' => 'A content',
        ]);

        Article::factory()->create([
            'content' => 'B content',
        ]);

        $url = route('api.v1.articles.index', ['sort' => 'content']);

        $this->getJson($url)
            ->assertSeeInOrder(['A content', 'B content', 'C content']);
    }

    /** @test */
    public function can_sort_articles_by_title_and_content(): void
    {

        Article::factory()->create([
            'title' => 'A Title',
            'content' => 'A content',
        ]);

        Article::factory()->create([
            'title' => 'B Title',
            'content' => 'B content',
        ]);

        Article::factory()->create([
            'title' => 'A Title',
            'content' => 'C content',
        ]);

        $url = route('api.v1.articles.index', ['sort' => 'title,-content']);

        $this->getJson($url)
            ->assertSeeInOrder(['C content', 'A content', 'B content']);
    }

    /** @test */
    public function cannot_sort_articles_by_unknown_fields(): void
    {

        Article::factory()->count(3)->create();

        $url = route('api.v1.articles.index', ['sort' => 'unknown']);

        $this->getJson($url)->assertJsonApiError(
            title: 'Bad Request',
            detail: "The sort field 'unknown' is not allowed in the 'articles' resource.",
            status: '400'
        );
    }
}
