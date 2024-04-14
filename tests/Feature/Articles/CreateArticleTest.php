<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function can_create_articles(): void
    {
        $category = Category::factory()->create();

        $response = $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New Article',
            'slug' => 'new-article',
            'content' => 'Some content',
            '_relationships' => [
                'category' => $category
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
        $this->postJson(route('api.v1.articles.store'), [
            'slug' => 'new-article',
            'content' => 'Some content'
        ])->assertJsonApiValidationErrors('title');
    }

       /** @test */
    public function title_must_be_at_least_4_characters(): void
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Tit',
            'slug' => 'new-article',
            'content' => 'Some content',
        ])->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function slug_is_required(): void
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New Article',
            'content' => 'Some content',
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_be_unique(): void
    {
        $article = Article::factory()->create();

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New Article',
            'slug' => $article->slug,
            'content' => 'Some content',
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_only_contain_letters_numbers_dashes(): void
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New Article',
            'slug' => '$%&^^%$#',
            'content' => 'Some content',
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_contain_underscore(): void
    {
        $this->postJson(route('api.v1.articles.store'), [
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
        $this->postJson(route('api.v1.articles.store'), [
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
        $this->postJson(route('api.v1.articles.store'), [
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
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New Article',
            'slug' => 'new-article',
        ])->assertJsonApiValidationErrors('content');
    }

    /** @test */
    public function category_relationship_is_required(): void
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New Article',
            'slug' => 'new-article',
            'content' => 'Some content',
        ])->assertJsonApiValidationErrors('relationships.category');
    }

    /** @test */
    public function category_must_be_exist_in_database(): void
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New Article',
            'slug' => 'new-article',
            'content' => 'Some content',
            '_relationships' => [
                'category' => Category::factory()->make()
            ]
        ])->assertJsonApiValidationErrors('relationships.category');
    }
}
