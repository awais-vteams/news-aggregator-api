<?php

namespace App\Domains\News\Actions;

use App\Domains\News\DTO\NewsDto;
use App\Domains\News\Models\Article;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;

class SaveArticleAction
{
    public function run(LazyCollection $newsCollection): void
    {
        DB::transaction(function () use ($newsCollection) {
            $newsCollection
                ->chunk(500)
                ->each(function ($chunk) {
                    $chunk->each(function (NewsDto $news) {
                        Article::updateOrCreate(
                            ['url' => $news->url],
                            $news->toArray()
                        );
                    });
                });
        });
    }
}
