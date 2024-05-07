<?php

namespace App\Exceptions\JsonApi;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotFoundHttpException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        $id = $request->input('data.id');
        $type = $request->input('data.type');

        return response()->json([
            'errors' => [
                [
                    'title' => 'Not Found',
                    'detail' => "No records found for '{$id}' in the '{$type}' resource.",
                    'status' => '404',
                ],
            ],
        ], 404);
    }
}
