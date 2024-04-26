<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;


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

    public function update(Article $article, Request $request)
    {
        $request->validate(['data.id' => 'exists:users,id']);
        
        $userId = $request->input('data.id');

        $article->update(['user_id' => $userId]);

        return AuthorResource::identifier($article->author);
    }
}
