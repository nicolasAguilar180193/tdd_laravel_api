<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_create_articles(): void
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'New Article',
                    'slug' => 'new-article',
                    'content' => 'Some content',
                ]
            ]
        ]);
        
        $response->assertCreated();

        $response->assertHeader('Location', route('api.v1.articles.show', Article::first()));

        $article = Article::first();

        $response->assertExactJson([
            'data' => [
                'type' => 'articles',
                'id' => (string) $article->getRouteKey(),
                'attributes' => [
                    'title' => 'New Article',
                    'slug' => 'new-article',
                    'content' => 'Some content',
                ],
                'links' => [
                    'self' => url(route('api.v1.articles.show', $article)),
                ]
            ]
        ]);

    }
}
