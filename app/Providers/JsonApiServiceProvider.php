<?php

namespace App\Providers;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class JsonApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Builder::macro('allowedSorts', function ($allowedSortFields) {
            if(request()->filled('sort')) {
                $sortFields = explode(',', request()->input('sort'));
                
                foreach($sortFields as &$sortField) {
                    $sortDirection = Str::of($sortField)->startsWith('-') ? 'desc' : 'asc';
                    
                    $sortField = ltrim($sortField, '-');
        
                    abort_unless(in_array($sortField, $allowedSortFields), 400);

                    /** @var Builder $this */
                    $this->orderBy($sortField, $sortDirection);
                }
            }

            return $this;
        });
    }
}
