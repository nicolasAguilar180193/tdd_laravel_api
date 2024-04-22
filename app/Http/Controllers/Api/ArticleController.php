<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Requests\SaveArticleRequest;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ArticleController extends Controller
{
    function show($article): JsonResource
    {
        $article = Article::where('slug', $article)
            ->allowedIncludes(['category'])
            ->sparseFields()
            ->firstOrFail();
        return ArticleResource::make($article);
    }

    function index(): AnonymousResourceCollection
    {
        $articles = Article::query()
            ->allowedIncludes(['category'])
            ->allowedFilters(['title','content','year','month'])
            ->allowedSorts(['title','content'])
            ->sparseFields()
            ->jsonPaginate();

        return ArticleResource::collection($articles);
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
