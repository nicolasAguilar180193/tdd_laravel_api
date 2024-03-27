<?php

namespace Tests\Feature;

use App\Http\Middleware\ValidateJsonApiHeaders;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ValidateJsonApiHeadersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::any('/json-api', fn () => 'ok')
            ->middleware(ValidateJsonApiHeaders::class);
    }

    /** @test */
    public function accept_headers_must_be_present_in_all_requests(): void
    {

        $this->get('/json-api')->assertStatus(406);

        $this->get('/json-api', [
            'accept' => 'application/vnd.api+json',
        ])->assertSuccessful();
    }

    /** @test */
    public function content_type_must_be_present_in_all_post_requests(): void
    {
        $this->post('/json-api', [], [
            'accept' => 'application/vnd.api+json'
        ])->assertStatus(415);

        $this->post('/json-api', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json'
        ])->assertSuccessful();
    }

    /** @test */
    public function content_type_must_be_present_in_all_patch_requests(): void
    {
        $this->patch('/json-api', [], [
            'accept' => 'application/vnd.api+json'
        ])->assertStatus(415);

        $this->patch('/json-api', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json'
        ])->assertSuccessful();
    }

    /** @test */
    public function content_type_header_must_be_presents_in_responses(): void
    {
        $this->get('/json-api', [
            'accept' => 'application/vnd.api+json',
        ])->assertHeader('Content-Type', 'application/vnd.api+json');

        $this->post('/json-api', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json'
        ])->assertHeader('Content-Type', 'application/vnd.api+json');

        $this->patch('/json-api', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json'
        ])->assertHeader('Content-Type', 'application/vnd.api+json');
    }

        /** @test */
        public function content_type_header_must_not_be_presents_in_empty_responses(): void
        {
            Route::any('/json-api', fn () => response()->noContent())
                ->middleware(ValidateJsonApiHeaders::class);

            $this->get('/json-api', [
                'accept' => 'application/vnd.api+json',
            ])->assertHeaderMissing('Content-Type');

            $this->post('/json-api', [], [
                'accept' => 'application/vnd.api+json',
                'content-type' => 'application/vnd.api+json'
            ])->assertHeaderMissing('Content-Type');

            $this->patch('/json-api', [], [
                'accept' => 'application/vnd.api+json',
                'content-type' => 'application/vnd.api+json'
            ])->assertHeaderMissing('Content-Type');

            $this->delete('/json-api', [], [
                'accept' => 'application/vnd.api+json',
                'content-type' => 'application/vnd.api+json'
            ])->assertHeaderMissing('Content-Type');
        }
}
