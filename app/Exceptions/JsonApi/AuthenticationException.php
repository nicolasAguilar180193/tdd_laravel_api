<?php

namespace App\Exceptions\JsonApi;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthenticationException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'errors' => [
                [
                    'title' => 'Unauthenticated',
                    'detail' => 'This action requires authentication.',
                    'status' => '401',
                ],
            ],
        ], 401);
    }
}
