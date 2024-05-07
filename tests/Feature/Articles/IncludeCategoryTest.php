<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IncludeCategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_include_related_category_of_an_article(): void
    {
        $article = Article::factory()->create();

        $url = route('api.v1.articles.show', [
            'article' => $article,
            'include' => 'category',
        ]);

        $this->getJson($url)->assertJson([
            'included' => [
                [
                    'type' => 'categories',
                    'id' => $article->category->getRouteKey(),
                    'attributes' => [
                        'name' => $article->category->name,
                    ],
                ],
            ],
        ]);
    }

    /** @test */
    public function can_include_related_category_of_multiple_article(): void
    {
        $article = Article::factory()->create()->load('category');
        $article2 = Article::factory()->create()->load('category');

        $url = route('api.v1.articles.index', [
            'include' => 'category',
        ]);

        $this->getJson($url)->assertJson([
            'included' => [
                [
                    'type' => 'categories',
                    'id' => $article->category->getRouteKey(),
                    'attributes' => [
                        'name' => $article->category->name,
                    ],
                ], [
                    'type' => 'categories',
                    'id' => $article2->category->getRouteKey(),
                    'attributes' => [
                        'name' => $article2->category->name,
                    ],
                ],
            ],
        ]);
    }

    /** @test */
    public function cannot_include_unknown_relationships(): void
    {
        $article = Article::factory()->create();

        // articles/the-slug?include=unknown,unknown2
        $url = route('api.v1.articles.show', [
            'article' => $article,
            'include' => 'unknown,unknown2',
        ]);

        $this->getJson($url)->assertStatus(400);

        // articles?include=unknown,unknown2
        $url = route('api.v1.articles.index', [
            'include' => 'unknown,unknown2',
        ]);

        $this->getJson($url)->assertJsonApiError(
            title: 'Bad Request',
            detail: "The included relationship 'unknown' is not allowed in the 'articles' resource.",
            status: '400'
        );
    }
}
