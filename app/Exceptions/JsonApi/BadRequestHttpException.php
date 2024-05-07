<?php

namespace App\Exceptions\JsonApi;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BadRequestHttpException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'errors' => [
                [
                    'title' => 'Bad Request',
                    'detail' => $this->getMessage(),
                    'status' => '400',
                ],
            ],
        ], 400);
    }
}
