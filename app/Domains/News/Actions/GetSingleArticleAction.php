<?php

namespace App\Domains\News\Actions;

use App\Domains\News\Models\Article;
use App\Domains\News\Resources\ArticleResource;
use Illuminate\Support\Facades\Cache;

class GetSingleArticleAction
{
    public function run(string $id)
    {
        $cacheKey = "article_{$id}";

        return Cache::remember($cacheKey, now()->addHour(), function () use ($id) {
            $article = Article::findOrFail($id);

            return new ArticleResource($article);
        });
    }
}
