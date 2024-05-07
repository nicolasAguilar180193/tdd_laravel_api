<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
                        'id' => $article->author->getRouteKey(),
                    ],
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
                        'name' => $article->author->name,
                    ],
                ],
            ]);
    }

    /** @test */
    public function can_update_the_associated_author(): void
    {
        $article = Article::factory()->create();

        $author = User::factory()->create();

        $url = route('api.v1.articles.relationships.author', $article);

        $response = $this->patchJson($url, [
            'data' => [
                'type' => 'authors',
                'id' => $author->getRouteKey(),
            ],
        ]);

        $response->assertExactJson([
            'data' => [
                'type' => 'authors',
                'id' => $author->getRouteKey(),
            ],
        ]);

        $this->assertDatabaseHas('articles', [
            'title' => $article->title,
            'user_id' => $author->id,
        ]);
    }

    /** @test */
    public function author_must_be_exist_in_databse(): void
    {
        $article = Article::factory()->create();

        $url = route('api.v1.articles.relationships.author', $article);

        $this->patchJson($url, [
            'data' => [
                'type' => 'authors',
                'id' => 'non-existing-author',
            ],
        ])->assertJsonApiValidationErrors('data.id');

        $this->assertDatabaseHas('articles', [
            'title' => $article->title,
            'user_id' => $article->user_id,
        ]);
    }
}
