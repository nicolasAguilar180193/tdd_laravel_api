<?php

namespace App\JsonApi\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


trait JsonApiResource
{
	abstract public function toJsonApi(): array;

    public function toArray(Request $request): array
    {
        return [
            'type' => $this->getResourceType(),
            'id' => $this->resource->getRouteKey(),
            'attributes' => $this->filterAttritutes($this->toJsonApi()),
            'links' => [
                'self' => url(
					route('api.v1.'.$this->getResourceType().'.show', $this->resource)
				)
            ]
        ];
    }

    public function withResponse(Request $request, JsonResponse $response)
    {
        $response->header(
            'Location',
            route('api.v1.'.$this->getResourceType().'.show', $this->resource)
        );
    }

    public function filterAttritutes(array $attributes): array
    {
        return array_filter($attributes, function ($value) {
            if(request()->isNotFilled('fields')) {
                return true;
            }

            $fields = explode(',', request('fields.'.$this->getResourceType()));

            if($value === $this->getRouteKey()) {
                return in_array($this->getRouteKeyName(), $fields);
            }

            return $value;
        });
    }

	public static function collection($resource): AnonymousResourceCollection
    {
        $collection = parent::collection($resource);

        $collection->with['links'] = ['self' => $resource->path()];

        return $collection;
    }
}