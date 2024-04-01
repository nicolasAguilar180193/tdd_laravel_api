<?php

namespace Tests;

use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert as PHPUnit;
use PHPUnit\Framework\ExpectationFailedException;
use Illuminate\Support\Str;

trait MakeJsonApiRequests
{
    protected bool $formatJsonApiDocument = true;
    protected function setUp(): void
    {
        parent::setUp();
        TestResponse::macro(
            'assertJsonApiValidationErrors',
            $this->assertJsonApiValidationErrors()
        );
    }

    public function withoutJsonApiDocumentFormatting(): void
    {
        $this->formatJsonApiDocument = false;
    }

    protected function assertJsonApiValidationErrors()
    {
        return function ($attribute) {
            /** @var TestResponse $this */
            
            $pointer = Str::of($attribute)->startsWith('data')
                ?  "/". str_replace('.', '/', $attribute)
                : "/data/attributes/{$attribute}";
            
            try {
                $this->assertJsonFragment([
                    'source' => ['pointer' => $pointer],
                ]);

            } catch (ExpectationFailedException $e) {
                PHPUnit::fail(
                    "Failed to find JSON:API validation errors for key: '{$attribute}'"
                    .PHP_EOL.PHP_EOL.
                    $e->getMessage()
                );
            }

            try {
                $this->assertJsonStructure([
                    'errors' => [
                        ['title', 'detail', 'source' => ['pointer']]
                    ]
                ]);

            } catch (ExpectationFailedException $e) {
                PHPUnit::fail(
                    "Failed to find a valid JSON:API error response"
                    .PHP_EOL.PHP_EOL.
                    $e->getMessage()
                );
            }
            
            $this->assertHeader(
                'Content-Type', 'application/vnd.api+json',
            )->assertStatus(422);
        };
    }

	public function json($method, $uri, array $data = [], array $headers = [], $options = 0): TestResponse
    {
        $headers['Accept'] = 'application/vnd.api+json';

        if($this->formatJsonApiDocument) {
            $formattedData['data']['attributes'] = $data;
            $formattedData['data']['type'] = (string) Str::of($uri)->after('/api/v1/');
        }

        return parent::json($method, $uri, $formattedData ?? $data, $headers, $options);
    }

    public function postJson($uri, array $data = [], array $headers = [], $options = 0): TestResponse
    {
        $headers['Content-Type'] = 'application/vnd.api+json';
        return parent::postJson($uri, $data, $headers, $options);
    }

    public function patchJson($uri, array $data = [], array $headers = [], $options = 0): TestResponse
    {
        $headers['Content-Type'] = 'application/vnd.api+json';
        return parent::patchJson($uri, $data, $headers, $options);
    }
}