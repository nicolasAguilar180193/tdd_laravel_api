<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\Category;
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

    /** @test */
    public function can_update_the_associated_category(): void
    {
        $article = Article::factory()->create();

        $category = Category::factory()->create();

        $url = route('api.v1.articles.relationships.category', $article);
        
        $response = $this->patchJson($url, [
            'data' => [
                'type' => 'categories',
                'id' => $category->getRouteKey()
            ]
        ]);

        $response->assertExactJson([
            'data' => [
                'type' => 'categories',
                'id' => $category->getRouteKey()
            ]
        ]);

        $this->assertDatabaseHas('articles', [
            'title' => $article->title,
            'category_id' => $category->id
        ]);
    }

    /** @test */
    public function category_must_be_exist_in_databse(): void
    {
        $article = Article::factory()->create();

        $url = route('api.v1.articles.relationships.category', $article);

        $this->patchJson($url, [
            'data' => [
                'type' => 'categories',
                'id' => 'non-existing-category'
            ]
        ])->assertJsonApiValidationErrors('data.id');

        $this->assertDatabaseHas('articles', [
            'title' => $article->title,
            'category_id' => $article->category_id
        ]);
    }
}
