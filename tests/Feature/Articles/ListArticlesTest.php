<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListArticlesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_fetch_a_single_article(): void
    {
        $article = Article::factory()->create();

        $response = $this->getJson(route('api.v1.articles.show', $article));
        
        $response->assertSuccessful();

        $response->assertJsonApiResource($article, [
            'title' => $article->title,
            'slug' => $article->slug,
            'content' => $article->content
        ])->assertJsonApiRelationshipsLinks($article, ['author', 'category']);
    }

    /** @test */
    public function can_fetch_all_articles(): void
    {
        $articles = Article::factory()->count(3)->create();

        $response = $this->getJson(route('api.v1.articles.index'));
        
        $response->assertJsonApiResourceCollection($articles, [
            'title', 'slug', 'content'
        ]);
    }

    /** @test */
    public function it_return_a_json_api_error_object_when_an_article_is_not_found(): void
    {
        $response = $this->getJson(route('api.v1.articles.show', 'non-existing'));
        
        $response->assertJsonStructure([
            'errors' => [
                '*' => []
            ]
        ])->assertJsonFragment([
            'title' => 'Not Found', 
            'detail' => "No records found for 'non-existing' in the 'articles' resource.", 
            'status' => '404'
        ])->assertStatus(404);
    }
}
