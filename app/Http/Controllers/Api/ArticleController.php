<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveArticleRequest;
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
        $articles = Article::query()
            ->allowedFilters(['title','content','year','month'])
            ->allowedSorts(['title','content'])
            ->jsonPaginate();

        return ArticleCollection::make($articles);
    }

    function store(SaveArticleRequest $request): ArticleResource
    {
        $article = Article::create($request->validated());
        
        return ArticleResource::make($article);
    }

    function update(Article $article, SaveArticleRequest $request): ArticleResource
    {        
        $article->update($request->validated());

        return ArticleResource::make($article);
    }

    function destroy(Article $article): Response
    {
        $article->delete();

        return response()->noContent();
    }
}
