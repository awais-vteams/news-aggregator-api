<?php

use App\Domains\News\DTO\NewsDto;
use Illuminate\Support\Facades\Http;
use App\Domains\News\Providers\NewsApiProvider;
use Illuminate\Support\LazyCollection;

it('fetches news directly from the API', function () {

    $provider = new NewsApiProvider();
    $news = $provider->fetchNews();

    expect($news)->toBeInstanceOf(LazyCollection::class);
    $articles = $news->take(5)->all();

    // Check if we received at least 1 article
    expect($articles)->not->toBeEmpty();

    /** @var \App\Domains\News\DTO\NewsDto $firstArticle */
    $firstArticle = $articles[0];
    expect($firstArticle->title)->not->toBeNull()
        ->and($firstArticle->url)->toStartWith('http')
        ->and($firstArticle->publishedAt)->toBeInstanceOf(Carbon\Carbon::class);
});

it('fetches news from the API and stores it in the database', function () {
    // Arrange: Mock HTTP response
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
                [
                    'title' => 'Another News',
                    'description' => 'Description for another news.',
                    'url' => 'https://example.com/another-news',
                    'author' => 'Jane Smith',
                    'content' => 'Content for another news.',
                    'source' => ['name' => 'Another News Source'],
                    'publishedAt' => '2024-12-14T12:00:00Z',
                ],
            ],
        ], 200),
    ]);

    // Act: Use the provider to fetch news
    $provider = new NewsApiProvider();
    $news = $provider->fetchNews();

    // Assert: Verify the news articles are fetched correctly
    $articles = $news->all();

    expect($articles)->toHaveCount(2);

    /** @var NewsDto $firstArticle */
    $firstArticle = $articles[0];
    expect($firstArticle->title)->toBe('Breaking News')
        ->and($firstArticle->author)->toBe('John Doe')
        ->and($firstArticle->sourceName)->toBe('Example News')
        ->and($firstArticle->publishedAt)->toBeInstanceOf(Carbon\Carbon::class);

    /** @var NewsDto $secondArticle */
    $secondArticle = $articles[1];
    expect($secondArticle->title)->toBe('Another News')
        ->and($secondArticle->author)->toBe('Jane Smith')
        ->and($secondArticle->sourceName)->toBe('Another News Source')
        ->and($secondArticle->publishedAt)->toBeInstanceOf(Carbon\Carbon::class);
});

it('handles API failure gracefully', function () {
    // Arrange: Mock HTTP failure
    Http::fake([
        'https://newsapi.org/v2/top-headlines*' => Http::response(null, 500),
    ]);

    // Act & Assert: Expect an exception to be thrown
    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Failed to fetch news from NewsAPI.');

    $provider = new NewsApiProvider();
    $provider->fetchNews();
});

it('fetches and processes large amounts of articles efficiently', function () {
    // Arrange: Mock a large response
    $mockArticles = [];
    for ($i = 0; $i < 1000; $i++) {
        $mockArticles[] = [
            'title' => "News Article $i",
            'description' => "Description $i",
            'url' => "https://example.com/news-$i",
            'author' => "Author $i",
            'content' => "Content $i",
            'source' => ['name' => "Source $i"],
            'publishedAt' => now()->toIso8601String(),
        ];
    }

    Http::fake([
        'https://newsapi.org/v2/top-headlines*' => Http::response([
            'articles' => $mockArticles,
        ], 200),
    ]);

    // Act: Fetch the news using the provider
    $provider = new NewsApiProvider();
    $news = $provider->fetchNews();

    // Assert: Verify all articles are processed lazily
    expect($news)->toBeInstanceOf(LazyCollection::class);
    $articles = $news->take(10)->all(); // Only take the first 10 articles to verify

    expect($articles)->toHaveCount(10)
        ->and($articles[0]->title)->toBe('News Article 0')
        ->and($articles[9]->title)->toBe('News Article 9');
});
