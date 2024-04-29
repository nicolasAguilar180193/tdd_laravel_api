<?php

namespace App\Exceptions;

use App\Http\Responses\JsonApiValidateErrorResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (NotFoundHttpException $e, $request) {
            $id = $request->input('data.id');
            $type = $request->input('data.type');

            return response()->json([
                'errors' => [
                    'title' => 'Not Found',
                    'detail' => "No records found for '{$id}' in the '{$type}' resource.", 
                    'status' => '404'
                ]
            ], 404);
        });
    }

    public function invalidJson($request, ValidationException $exception)
    {
        return new JsonApiValidateErrorResponse($exception);
    }
}
