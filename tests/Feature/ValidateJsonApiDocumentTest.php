<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ValidateJsonApiDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ValidateJsonApiDocumentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutJsonApiDocumentFormatting();

        Route::any('/json-api', fn () => 'ok')
            ->middleware(ValidateJsonApiDocument::class);
    }

    /** @test */
    public function data_is_required(): void
    {
        $this->postJson('/json-api', [])
            ->assertJsonApiValidationErrors('data');

        $this->patchJson('/json-api', [])
            ->assertJsonApiValidationErrors('data');
    }

    /** @test */
    public function data_must_be_an_array(): void
    {
        $this->postJson('/json-api', ['data' => 'data'])
            ->assertJsonApiValidationErrors('data');

        $this->patchJson('/json-api', ['data' => 'data'])
            ->assertJsonApiValidationErrors('data');
    }

    /** @test */
    public function data_type_is_required(): void
    {
        $this->postJson('/json-api', [
            'data' => [
                'attributes' => ['title' => 'title'],
            ],
        ])->assertJsonApiValidationErrors('data.type');

        $this->patchJson('/json-api', [
            'data' => [
                'attributes' => ['title' => 'title'],
            ],
        ])->assertJsonApiValidationErrors('data.type');
    }

    /** @test */
    public function data_type_must_be_an_string(): void
    {
        $this->postJson('/json-api', [
            'data' => [
                'type' => 1,
                'attributes' => ['title' => 'title'],
            ],
        ])->assertJsonApiValidationErrors('data.type');

        $this->patchJson('/json-api', [
            'data' => [
                'type' => 1,
                'attributes' => ['title' => 'title'],
            ],
        ])->assertJsonApiValidationErrors('data.type');
    }

    /** @test */
    public function data_attributes_is_required(): void
    {
        $this->postJson('/json-api', [
            'data' => [
                'type' => 'articles',
            ],
        ])->assertJsonApiValidationErrors('data.attributes');

        $this->patchJson('/json-api', [
            'data' => [
                'type' => 'articles',
            ],
        ])->assertJsonApiValidationErrors('data.attributes');
    }

    /** @test */
    public function data_attributes_must_be_an_array(): void
    {
        $this->postJson('/json-api', [
            'data' => [
                'type' => 'articles',
                'attributes' => 'attributes',
            ],
        ])->assertJsonApiValidationErrors('data.attributes');

        $this->patchJson('/json-api', [
            'data' => [
                'type' => 'articles',
                'attributes' => 'attributes',
            ],
        ])->assertJsonApiValidationErrors('data.attributes');
    }

    /** @test */
    public function data_id_is_required(): void
    {
        $this->patchJson('/json-api', [
            'data' => [
                'type' => 'articles',
                'attributes' => ['title' => 'title'],
            ],
        ])->assertJsonApiValidationErrors('data.id');
    }

    /** @test */
    public function data_id_must_be_a_string(): void
    {
        $this->patchJson('/json-api', [
            'data' => [
                'type' => 'articles',
                'attributes' => ['title' => 'title'],
                'id' => 1,
            ],
        ])->assertJsonApiValidationErrors('data.id');
    }

    /** @test */
    public function only_accepts_valid_json_api_document(): void
    {
        $this->postJson('/json-api', [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'title',
                ],
            ],
        ])->assertSuccessful();

        $this->postJson('/json-api', [
            'data' => [
                'type' => 'articles',
                'id' => 1,
                'attributes' => [
                    'title' => 'title',
                ],
            ],
        ])->assertSuccessful();
    }
}
