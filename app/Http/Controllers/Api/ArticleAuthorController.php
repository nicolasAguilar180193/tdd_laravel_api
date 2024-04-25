<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleAuthorController extends Controller
{
    public function index(Article $article): Array
    {
        return AuthorResource::identifier($article->author);
    }

    public function show(Article $article): AuthorResource
    {
        return AuthorResource::make($article->author);
    }
}
