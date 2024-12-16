<?php

use App\Domains\News\Providers\NewsApiProvider;
use App\Domains\News\DTO\NewsDto;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

it('fetches news successfully from NewsAPI', function () {
    // Mock the HTTP response
    Http::fake([
        'https://newsapi.org/v2/top-headlines*' => Http::response([
            'articles' => [
                [
                    'title' => 'Breaking News',
                    'description' => 'This is a breaking news description.',
                    'url' => 'https://example.com/breaking-news',
                    'author' => 'John Doe',
                    'content' => 'Full content of the breaking news.',
                    'source' => ['name' => 'Example News'],
                    'publishedAt' => '2024-12-14T10:00:00Z',
                ],
            ],
        ], 200),
    ]);

    $provider = new NewsApiProvider();

    $news = $provider->fetchNews();
    $articles = $news->all();

    expect($articles)->toHaveCount(1);

    /** @var NewsDto $article */
    $article = $articles[0];
    expect($article)->toBeInstanceOf(NewsDto::class)
        ->and($article->title)->toBe('Breaking News')
        ->and($article->description)->toBe('This is a breaking news description.')
        ->and($article->url)->toBe('https://example.com/breaking-news')
        ->and($article->author)->toBe('John Doe')
        ->and($article->content)->toBe('Full content of the breaking news.')
        ->and($article->category)->toBe('top-headlines')
        ->and($article->sourceName)->toBe('Example News')
        ->and($article->publishedAt)->toBeInstanceOf(Carbon::class);
});

it('handles missing fields in articles', function () {
    Http::fake([
        'https://newsapi.org/v2/top-headlines*' => Http::response([
            'articles' => [
                [
                    'title' => 'News Without Author',
                    'url' => 'https://example.com/news-without-author',
                    'publishedAt' => '2024-12-14T10:00:00Z',
                ],
            ],
        ], 200),
    ]);

    $provider = new NewsApiProvider();

    $news = $provider->fetchNews();
    $articles = $news->all();

    expect($articles)->toHaveCount(1);

    /** @var NewsDto $article */
    $article = $articles[0];
    expect($article->title)->toBe('News Without Author')
        ->and($article->author)->toBeNull()
        ->and($article->description)->toBeNull()
        ->and($article->content)->toBeNull()
        ->and($article->sourceName)->toBeNull();
});

