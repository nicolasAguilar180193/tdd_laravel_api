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
        ]);
    }

    /** @test */
    public function can_fetch_a_list_of_articles(): void
    {
        $this->withoutExceptionHandling();

        $articles = Article::factory()->count(3)->create();

        $response = $this->getJson(route('api.v1.articles.index'));
        
        $response->assertJsonApiResourceCollection($articles, [
            'title', 'slug', 'content'
        ]);
    }     
}
