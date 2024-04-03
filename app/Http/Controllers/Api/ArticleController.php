<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ArticleController extends Controller
{
    function show(Article $article): ArticleResource
    {
        return ArticleResource::make($article);
    }

    function index(): ArticleCollection
    {
        return ArticleCollection::make(Article::all());
    }

    function store(Request $request): ArticleResource
    {
        $request->validate([
            'data.attributes.title' => 'required|min:4',
            'data.attributes.slug' => 'required',
            'data.attributes.content' => 'required',
        ]);

        $article = Article::create([
            'title' => $request->input('data.attributes.title'),
            'slug' => $request->input('data.attributes.slug'),
            'content' => $request->input('data.attributes.content'),
        ]);
        
        return ArticleResource::make($article);
    }

    function update(Article $article, Request $request): ArticleResource
    {
        $request->validate([
            'data.attributes.title' => 'required|min:4',
            'data.attributes.slug' => 'required',
            'data.attributes.content' => 'required',
        ]);
        
        $article->update([
            'title' => $request->input('data.attributes.title'),
            'slug' => $request->input('data.attributes.slug'),
            'content' => $request->input('data.attributes.content'),  
        ]);

        return ArticleResource::make($article);
    }

    function destroy(Article $article): Response
    {
        $article->delete();

        return response()->noContent();
    }
}
