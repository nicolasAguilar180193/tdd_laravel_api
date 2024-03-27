<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListArticlesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_fetch_a_single_article(): void
    {
        $this->withoutExceptionHandling();

        $article = Article::factory()->create();

        $response = $this->getJson(route('api.v1.articles.show', $article));
        
        $response->assertSuccessful();

        $response->assertExactJson([
            'data' => [
                'type' => 'article',
                'id' => $article->getRouteKey(),
                'attributes' => [
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'content' => $article->content
                ],
                'links' => [
                    'self' => url(route('api.v1.articles.show', $article))
                ]
            ]
        ]);
    }
}
