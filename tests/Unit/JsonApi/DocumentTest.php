<?php

namespace Tests\Unit\JsonApi;

use App\Models\Category;
use App\JsonApi\Document;
use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class DocumentTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function can_create_json_api_documents(): void
    {
        $category = Mockery::mock('Category', function ($mock) {
            $mock->shouldReceive('getResourceType')->andReturn('categories');
            $mock->shouldReceive('getRouteKey')->andReturn('category-id');
        });

        $document = Document::type('articles')
            ->id('article-id')
            ->attributes([
                'title' => 'title'
            ])->relationships([
                'category' => $category
            ])->toArray();

        $expected = [
            'data' => [
                'type' => 'articles',
                'id' => 'article-id',
                'attributes' => [
                    'title' => 'title'
                ],
                'relationships' => [
                    'category' => [
                        'data' => [
                            'type' => 'categories',
                            'id' => 'category-id'
                        ]
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $document);
    }
}
