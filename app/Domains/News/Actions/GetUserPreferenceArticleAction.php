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
            foreach ($preferences->sources as $source) {
                $query->orWhere('source_name', 'ilike', '%'.$source.'%');
            }
        }

        if (filled($preferences->categories)) {
            foreach ($preferences->categories as $category) {
                $query->orWhere('category', 'ilike', '%'.$category.'%');
            }
        }

        if (filled($preferences->authors)) {
            foreach ($preferences->authors as $author) {
                $query->orWhere('author', 'ilike', '%'.$author.'%');
            }
        }

        $cacheKey = 'user_'.$preferences->user_id.'_personalized_articles';

        return Cache::remember($cacheKey, now()->addHour(), function () use ($query) {
            return ArticleResource::collection($query->orderBy('published_at', 'desc')->paginate());
        });
    }
}
