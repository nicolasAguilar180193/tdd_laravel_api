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
                'type' => 'articles',
                'id' => (string) $article->getRouteKey(),
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

    /** @test */
    public function can_fetch_a_list_of_articles(): void
    {
        $this->withoutExceptionHandling();

        $articles = Article::factory()->count(3)->create();

        $response = $this->getJson(route('api.v1.articles.index'));
        
        $response->assertExactJson([
            'data' => [
                [
                    'type' => 'articles',
                    'id' => $articles[0]->getRouteKey(),
                    'attributes' => [
                        'title' => $articles[0]->title,
                        'slug' => $articles[0]->slug,
                        'content' => $articles[0]->content
                    ],
                    'links' => [
                        'self' => url(route('api.v1.articles.show', $articles[0]))
                        ]
                    ],
                [
                    'type' => 'articles',
                    'id' => $articles[1]->getRouteKey(),
                    'attributes' => [
                        'slug' => $articles[1]->slug,
                        'title' => $articles[1]->title,
                        'content' => $articles[1]->content
                    ],
                    'links' => [
                        'self' => url(route('api.v1.articles.show', $articles[1]))
                    ]
                ],
                [
                    'type' => 'articles',
                    'id' => $articles[2]->getRouteKey(),
                    'attributes' => [
                        'title' => $articles[2]->title,
                        'slug' => $articles[2]->slug,
                        'content' => $articles[2]->content
                    ],
                    'links' => [
                        'self' => url(route('api.v1.articles.show', $articles[2]))
                    ]
                ]
            ],
            'links' => [
                'self' => url(route('api.v1.articles.index'))
            ]
        ]);
    }     
}
