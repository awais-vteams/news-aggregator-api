<?php

namespace App\Domains\News\Actions;

use App\Domains\News\DTO\ArticleFilterDto;
use App\Domains\News\Models\Article;
use App\Domains\News\Requests\AllArticleRequest;
use App\Domains\News\Resources\ArticleResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class GetAllArticleAction
{
    public function run(AllArticleRequest $request)
    {
        $filters = $request->only(['keyword', 'date', 'category', 'source']);
        $page = $request->get('page', 1);
        $cacheKey = 'articles_'.md5(json_encode($filters)).'_page_'.$page;

        $filterDto = new ArticleFilterDto(
            keyword: $filters['keyword'] ?? null,
            date: filled($filters['date'] ?? null) ? Carbon::parse($filters['date']) : null,
            category: $filters['category'] ?? null,
            source: $filters['source'] ?? null,
        );

        return Cache::remember($cacheKey, now()->addHour(), function () use ($filterDto) {
            $articles = Article::query()
                ->filter($filterDto)
                ->orderBy('published_at', 'desc')
                ->paginate();

            return ArticleResource::collection($articles);
        });
    }
}
