<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateArcticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_update_owned_articles(): void
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author, ['articles:update']);

        $response = $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Updated Article',
            'slug' => $article->slug,
            'content' => 'Updated content'
        ])->assertOk();

        $response->assertJsonApiResource($article, [
            'title' => 'Updated Article',
            'slug' => $article->slug,
            'content' => 'Updated content'
        ]);
    }

    /** @test */
    public function cannot_update_articles_owned_by_others_users(): void
    {
        $article = Article::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Updated Article',
            'slug' => $article->slug,
            'content' => 'Updated content'
        ])->assertForbidden();
    }

    /** @test */
    public function guests_cannot_update_articles(): void
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article))
            ->assertJsonApiError(
                title: 'Unauthenticated',
                detail: 'This action requires authentication.',
                status: '401',
            );
    }

    /** @test */
    public function title_is_required(): void
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);

        $this->patchJson(route('api.v1.articles.update', $article), [
            'slug' => 'updated-article',
            'content' => 'Updated content'
        ])->assertJsonApiValidationErrors('title');
    }

        /** @test */
    public function title_must_be_at_least_4_characters(): void
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);
        
        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Tit',
            'slug' => 'updated-article',
            'content' => 'Updated content',
        ])->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function slug_is_required(): void
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);
        
        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Updated Article',
            'content' => 'Updated content',
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_be_unique(): void
    {
        $article1 = Article::factory()->create();
        $article2 = Article::factory()->create();

        Sanctum::actingAs($article1->author);
        
        $this->patchJson(route('api.v1.articles.update', $article1), [
            'title' => 'New Article',
            'slug' => $article2->slug,
            'content' => 'Some content',
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_only_contain_letters_numbers_dashes(): void
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);
        
        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'New Article',
            'slug' => '$%&^^%$#',
            'content' => 'Some content',
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_contain_underscore(): void
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);
        
        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'New Article',
            'slug' => 'new_article',
            'content' => 'Some content',
        ])->assertSee(__('validation.no_underscores', [
            'attribute' => 'data.attributes.slug'
        ]))->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_start_with_dashes(): void
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);
        
        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'New Article',
            'slug' => '-new-article',
            'content' => 'Some content',
        ])->assertSee(__('validation.no_starting_dashes', [
            'attribute' => 'data.attributes.slug'
        ]))->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_end_with_dashes(): void
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);
        
        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'New Article',
            'slug' => 'new-article-',
            'content' => 'Some content',
        ])->assertSee(__('validation.no_ending_dashes', [
            'attribute' => 'data.attributes.slug'
        ]))->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function content_is_required(): void
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);
        
        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Updated Article',
            'slug' => 'updated-article',
        ])->assertJsonApiValidationErrors('content');
    }
}
