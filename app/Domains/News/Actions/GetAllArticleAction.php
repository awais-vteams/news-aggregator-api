<?php

namespace App\Domains\News\Actions;

use App\Domains\News\Models\Article;
use App\Domains\News\Requests\AllArticleRequest;
use App\Domains\News\Resources\ArticleResource;
use Illuminate\Support\Facades\Cache;

class GetAllArticleAction
{
    public function run(AllArticleRequest $request)
    {
        $filters = $request->only(['keyword', 'date', 'category', 'source']);
        $page = $request->get('page', 1);
        $cacheKey = 'articles_'.md5(json_encode($filters)).'_page_'.$page;

        return Cache::remember($cacheKey, now()->addHour(), function () use ($filters) {
            $articles = Article::query()
                ->filter($filters)
                ->orderBy('published_at', 'desc')
                ->paginate();

            return ArticleResource::collection($articles);
        });
    }
}
