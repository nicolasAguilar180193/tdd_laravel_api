<?php

namespace App\JsonApi\Traits;

use App\JsonApi\Document;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\MissingValue;

trait JsonApiResource
{
	abstract public function toJsonApi(): array;

    public static function identifier($resource): Array
    {
        return Document::type($resource->getResourceType())
            ->id($resource->getRouteKey())
            ->toArray();
    }

    public function toArray(Request $request): array
    {
        if($request->filled('include')) {
            foreach($this->getIncludes() as $include) {
                if($include->resource instanceof MissingValue) {
                    continue;
                }
                $this->with['included'][] = $include;
            }
        }

        return Document::type($this->resource->getResourceType())
            ->id($this->resource->getRouteKey())
            ->attributes($this->filterAttritutes($this->toJsonApi()))
            ->relationshipsLinks($this->getRelationshipsLinks())
            ->links([
                'self' => route('api.v1.'.$this->resource->getResourceType().'.show', $this->resource)
            ])->get('data');
    }

    public function getIncludes(): array
    {
        return [];
    }

    public function getRelationshipsLinks(): array
    {
        return [];
    }

    public function withResponse(Request $request, JsonResponse $response)
    {
        $response->header(
            'Location',
            route('api.v1.'.$this->resource->getResourceType().'.show', $this->resource)
        );
    }

    public function filterAttritutes(array $attributes): array
    {
        return array_filter($attributes, function ($value) {
            if(request()->isNotFilled('fields')) {
                return true;
            }

            $fields = explode(',', request('fields.'.$this->resource->getResourceType()));

            if($value === $this->getRouteKey()) {
                return in_array($this->getRouteKeyName(), $fields);
            }

            return $value;
        });
    }

	public static function collection($resources): AnonymousResourceCollection
    {
        $collection = parent::collection($resources);

        if(request()->filled('include')) {
            foreach($resources as $resource) {
                foreach($resource->getIncludes() as $include) {
                    if($include->resource instanceof MissingValue) {
                        continue;
                    }
                    $collection->with['included'][] = $include;
                }
            }
        }

        $collection->with['links'] = ['self' => $resources->path()];

        return $collection;
    }
}