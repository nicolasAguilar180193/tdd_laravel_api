<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class JsonApiValidateErrorResponse extends JsonResponse
{
	public function __construct(ValidationException $exception, $status = 422)
	{
		$data = $this->formatJsonApiErrors($exception);
		$headers = ['Content-Type' => 'application/vnd.api+json'];

		parent::__construct($data, $status, $headers);
	}


	protected function formatJsonApiErrors($exception)
	{
		$title = $exception->getMessage();
        $errors = [];
        
        foreach ($exception->errors() as $field => $messages) {

            $errors[] =  [
                'title' => $title,
                'detail' => $messages[0],
                'source' => [
                    'pointer' => '/' . str_replace('.', '/', $field),
                ]
            ];
        }

		return ['errors' => $errors];
	}

}