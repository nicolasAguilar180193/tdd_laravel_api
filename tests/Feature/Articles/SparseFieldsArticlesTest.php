<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SparseFieldsArticlesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function specific_fields_can_be_requested_in_the_article_index(): void
    {
        $article = Article::factory()->create();

        // articles?fileds[articles]=title,slug
        $url = route('api.v1.articles.index', [
            'fields' => [
                'articles' => 'title,slug',
            ]
        ]);

        $this->getJson($url)
            ->assertJsonFragment([
                'title' => $article->title,
                'slug' => $article->slug,
            ])->assertJsonMissing([
                'content' => $article->content
            ])->assertJsonMissing([
                'content' => null
            ]);
    }

    /** @test */
    public function route_key_must_be_added_automatically_in_the_article_index(): void
    {
        $article = Article::factory()->create();

        // articles?fileds[articles]=title
        $url = route('api.v1.articles.index', [
            'fields' => [
                'articles' => 'title',
            ]
        ]);

        $this->getJson($url)
            ->assertJsonFragment([
                'title' => $article->title,
            ])->assertJsonMissing([
                'content' => $article->content,
                'slug' => $article->slug
            ]);
    }

    /** @test */
    public function specific_fields_can_be_requested_in_the_article_show(): void
    {
        $article = Article::factory()->create();

        // articles/the-slug?fileds[articles]=title,slug
        $url = route('api.v1.articles.show', [
            'article' => $article,
            'fields' => [
                'articles' => 'title,slug',
            ]
        ]);

        $this->getJson($url)
            ->assertJsonFragment([
                'title' => $article->title,
                'slug' => $article->slug,
            ])->assertJsonMissing([
                'content' => $article->content
            ])->assertJsonMissing([
                'content' => null
            ]);
    }

    /** @test */
    public function route_key_must_be_added_automatically_in_the_article_show(): void
    {
        $article = Article::factory()->create();

        // articles/the-slug?fileds[articles]=title
        $url = route('api.v1.articles.show', [
            'article' => $article,
            'fields' => [
                'articles' => 'title',
            ]
        ]);

        $this->getJson($url)
            ->assertJsonFragment([
                'title' => $article->title,
            ])->assertJsonMissing([
                'content' => $article->content,
                'slug' => $article->slug
            ]);
    }
}
