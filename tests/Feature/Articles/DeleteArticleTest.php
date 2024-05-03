<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_delete_article(): void
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);

        $this->deleteJson(route('api.v1.articles.destroy', $article))
            ->assertNoContent();

        $this->assertDatabaseCount('articles', 0);
    }

    /** @test */
    public function guests_cannot_delete_article(): void
    {
        $article = Article::factory()->create();

        $this->deleteJson(route('api.v1.articles.destroy', $article))
            ->assertUnauthorized();

        $this->assertDatabaseCount('articles', 1);
    }
}
