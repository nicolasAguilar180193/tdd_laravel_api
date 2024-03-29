<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function can_create_articles(): void
    {
        $response = $this->postJson(route('api.v1.articles.store'), [
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

    /** @test */
    public function title_is_required(): void
    {
        $response = $this->postJson(route('api.v1.articles.store'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'slug' => 'new-article',
                    'content' => 'Some content',
                ]
            ]
        ]);

        $response->assertJsonApiValidationErrors('title');
    }

       /** @test */
    public function title_must_be_at_least_4_characters(): void
    {
        $response = $this->postJson(route('api.v1.articles.store'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'Tit',
                    'slug' => 'new-article',
                    'content' => 'Some content',
                ]
            ]
        ]);

        $response->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function slug_is_required(): void
    {
        $response = $this->postJson(route('api.v1.articles.store'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'New Article',
                    'content' => 'Some content',
                ]
            ]
        ]);

        $response->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function content_is_required(): void
    {
        $response = $this->postJson(route('api.v1.articles.store'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'New Article',
                    'slug' => 'new-article',
                ]
            ]
        ]);

        $response->assertJsonApiValidationErrors('content');
    }
}
