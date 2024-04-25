<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthorRelationshipTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_fetch_the_associated_author_identifier(): void
    {
        $article = Article::factory()->create();

        $url = route('api.v1.articles.relationships.author', $article);

        $this->getJson($url)
            ->assertExactJson(
                [
                    'data' => [
                        'type' => 'authors',
                        'id' => $article->author->getRouteKey()
                    ]
                ]
        );
    }

    /** @test */
    public function can_fetch_the_associated_author_resource(): void
    {
        $article = Article::factory()->create();

        $url = route('api.v1.articles.author', $article);
    
        $this->getJson($url)
            ->assertJson([
                'data' => [
                    'type' => 'authors',
                    'id' => $article->author->getRouteKey(),
                    'attributes' => [
                        'name' => $article->author->name
                    ]
                ]
            ]);
    }
}
