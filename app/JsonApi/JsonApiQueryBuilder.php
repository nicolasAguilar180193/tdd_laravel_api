<?php

namespace App\JsonApi;

use Closure;
use Illuminate\Support\Str;

class JsonApiQueryBuilder
{

	public function allowedSorts(): Closure
	{
		return function ($allowedSortFields) {
			/** @var Builder $this */
            if(request()->filled('sort')) {
                $sortFields = explode(',', request()->input('sort'));
                
                foreach($sortFields as &$sortField) {
                    $sortDirection = Str::of($sortField)->startsWith('-') ? 'desc' : 'asc';
                    
                    $sortField = ltrim($sortField, '-');
        
                    abort_unless(in_array($sortField, $allowedSortFields), 400);

                    $this->orderBy($sortField, $sortDirection);
                }
            }

            return $this;
        };
	}

	public function jsonPaginate(): Closure
	{
		return function() {
            /** @var Builder $this */
            return $this->paginate(
                $perPage = request('page.size', 15), 
                $columns = ['*'], 
                $pageName = 'page[number]', 
                $page = request('page.number', 1)
            )->appends(request()->only('page.size', 'sort'));
        };
	}
}