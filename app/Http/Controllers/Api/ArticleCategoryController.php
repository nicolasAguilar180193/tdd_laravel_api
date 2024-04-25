<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Article;

class ArticleCategoryController extends Controller
{
    public function index(Article $article): Array
    {
        return CategoryResource::identifier($article->category);
    }

    public function show(Article $article): CategoryResource
    {
        return CategoryResource::make($article->category);
    }
}
