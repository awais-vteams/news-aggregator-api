<?php

use App\Domains\News\Commands\FetchNews;
use App\Domains\News\Models\Article;
use Illuminate\Support\Facades\Artisan;

it('fetches news from all providers and saves articles in the database', function () {

    // Assert: No articles exist before running the command
    expect(Article::count())->toBe(0);

    // Act: Run the console command
    Artisan::call(FetchNews::class);

    // Assert: Articles are saved in the database
    $articleCount = Article::count();
    expect($articleCount)->toBeGreaterThan(0);

    // Verify an example article's structure
    $article = Article::first();
    expect($article)->toHaveKeys([
        'title',
        'description',
        'url',
        'author',
        'category',
        'source_name',
        'published_at',
    ]);
});
