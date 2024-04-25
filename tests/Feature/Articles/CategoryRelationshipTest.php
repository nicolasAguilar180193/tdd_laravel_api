<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryRelationshipTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_fetch_the_associated_category_identifier(): void
    {
        $article = Article::factory()->create();

        $url = route('api.v1.articles.relationships.category', $article);

        $this->getJson($url)
            ->assertExactJson(
                [
                    'data' => [
                        'type' => 'categories',
                        'id' => $article->category->getRouteKey()
                    ]
                ]
        );
    }

    /** @test */
    public function can_fetch_the_associated_category_resource(): void
    {
        $article = Article::factory()->create();

        $url = route('api.v1.articles.category', $article);
    
        $this->getJson($url)
            ->assertJson([
                'data' => [
                    'type' => 'categories',
                    'id' => $article->category->getRouteKey(),
                    'attributes' => [
                        'name' => $article->category->name
                    ]
                ]
            ]);
    }
}
