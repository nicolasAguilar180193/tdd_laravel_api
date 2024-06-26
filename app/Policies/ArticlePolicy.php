<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Article;

class ArticlePolicy
{
    public function create(User $user): bool
    {
        return $user->tokenCan('articles:create');
    }

    public function update(User $user, Article $article): bool
    {
        return $user->is($article->author) && $user->tokenCan('articles:update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Article $article): bool
    {
        return $user->is($article->author) && $user->tokenCan('articles:delete');
    }
}
