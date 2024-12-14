<?php

namespace App\Domains\News\Actions;

use App\Domains\News\Models\Article;
use App\Domains\News\Models\UserPreference;
use App\Domains\News\Resources\ArticleResource;
use Illuminate\Support\Facades\Cache;

class GetUserPreferenceArticleAction
{
    public function run(UserPreference $preferences)
    {
        $query = Article::query();

        if (filled($preferences->sources)) {
            $query->whereIn('source_name', $preferences->sources);
        }

        if (filled($preferences->categories)) {
            $query->whereIn('category', $preferences->categories);
        }

        if (filled($preferences->authors)) {
            $query->whereIn('author', $preferences->authors);
        }

        $cacheKey = 'user_'.$preferences->user_id.'_personalized_articles';

        return Cache::remember($cacheKey, now()->addHour(), function () use ($query) {
            return ArticleResource::collection($query->orderBy('published_at', 'desc')->paginate());
        });
    }
}
