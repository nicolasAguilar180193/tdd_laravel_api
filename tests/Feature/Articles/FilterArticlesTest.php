<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilterArticlesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_filter_articles_by_title(): void
    {
        Article::factory()->create([
            'title' => 'Article Laravel',
        ]);

        Article::factory()->create([
            'title' => 'Other Article',
        ]);

        $url = route('api.v1.articles.index', [
            'filter' => [
                'title' => 'Laravel',
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Article Laravel')
            ->assertDontSee('Other Article');
    }

    /** @test */
    public function can_filter_articles_by_content(): void
    {
        Article::factory()->create([
            'content' => 'Article Laravel',
        ]);

        Article::factory()->create([
            'content' => 'Other Article',
        ]);

        $url = route('api.v1.articles.index', [
            'filter' => [
                'content' => 'Laravel',
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Article Laravel')
            ->assertDontSee('Other Article');
    }

    /** @test */
    public function can_filter_articles_by_year(): void
    {
        Article::factory()->create([
            'title' => 'Article from 2021',
            'created_at' => now()->year(2021),
        ]);

        Article::factory()->create([
            'title' => 'Article from 2022',
            'created_at' => now()->year(2022),
        ]);

        $url = route('api.v1.articles.index', [
            'filter' => [
                'year' => '2021',
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Article from 2021')
            ->assertDontSee('Article from 2022');
    }

    /** @test */
    public function can_filter_articles_by_month(): void
    {
        Article::factory()->create([
            'title' => 'Article from month 3',
            'created_at' => now()->month(3),
        ]);

        Article::factory()->create([
            'title' => 'Another Article from month 3',
            'created_at' => now()->month(3),
        ]);

        Article::factory()->create([
            'title' => 'Article from month 1',
            'created_at' => now()->month(1),
        ]);

        $url = route('api.v1.articles.index', [
            'filter' => [
                'month' => '3',
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(2, 'data')
            ->assertSee('Article from month 3')
            ->assertSee('Another Article from month 3')
            ->assertDontSee('Article from month 1');
    }

    /** @test */
    public function cannot_filter_articles_by_unknown_filters(): void
    {
        Article::factory()->count(2)->create();

        $url = route('api.v1.articles.index', [
            'filter' => [
                'unknown' => 'filter',
            ]
        ]);

        $this->getJson($url)->assertJsonApiError(
            title: 'Bad Request', 
            detail: "The filter 'unknown' is not allowed in the 'articles' resource.", 
            status: '400'
        );
    }

    /** @test */
    public function can_filter_articles_by_category(): void
    {
        Article::factory()->count(2)->create();
        $cat1 = Category::factory()->hasArticles(3)->create(['slug' => 'cat-1']);
        $cat2 = Category::factory()->hasArticles()->create(['slug' => 'cat-2']);

        // articles?filter[categories]=cat-1
        $url = route('api.v1.articles.index', [
            'filter' => [
                'categories' => 'cat-1,cat-2',
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(4, 'data')
            ->assertSee($cat1->articles[0]->title)
            ->assertSee($cat1->articles[0]->title)
            ->assertSee($cat1->articles[0]->title)
            ->assertSee($cat2->articles[0]->title);
    }
    
}
