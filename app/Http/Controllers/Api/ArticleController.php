<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Requests\SaveArticleRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ArticleController extends Controller
{

    public function __construct() {
        $this->middleware('auth:sanctum')
            ->only(['store', 'update', 'destroy']);
    }

    function show($article): JsonResource
    {
        $article = Article::where('slug', $article)
            ->allowedIncludes(['category', 'author'])
            ->sparseFields()
            ->firstOrFail();
        return ArticleResource::make($article);
    }

    function index(): AnonymousResourceCollection
    {
        $articles = Article::query()
            ->allowedIncludes(['category', 'author'])
            ->allowedFilters(['title','content','year','month', 'categories'])
            ->allowedSorts(['title','content'])
            ->sparseFields()
            ->jsonPaginate();

        return ArticleResource::collection($articles);
    }

    function store(SaveArticleRequest $request): ArticleResource
    {
        $this->authorize('create', new Article);

        $article = Article::create($request->validated());
        
        return ArticleResource::make($article);
    }

    function update(Article $article, SaveArticleRequest $request): ArticleResource
    {
        $this->authorize('update', $article);

        $article->update($request->validated());

        return ArticleResource::make($article);
    }

    function destroy(Article $article, Request $request): Response
    {
        $this->authorize('delete', $article);
        
        $article->delete();

        return response()->noContent();
    }
}
