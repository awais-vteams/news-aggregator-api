<?php

use App\Domains\News\DTO\NewsDto;
use App\Domains\News\Providers\NyTimesProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\LazyCollection;

it('fetches real news from the NY Times API', function () {

    $provider = new NyTimesProvider();
    $news = $provider->fetchNews();

    // Assert: Validate the results
    expect($news)->toBeInstanceOf(LazyCollection::class);
    $articles = $news->take(5)->all();

    expect($articles)->not->toBeEmpty();

    /** @var NewsDto $firstArticle */
    $firstArticle = $articles[0];
    expect($firstArticle->title)->not->toBeNull()
        ->and($firstArticle->url)->toStartWith('http')
        ->and($firstArticle->publishedAt)->toBeInstanceOf(Carbon\Carbon::class);
});

it('fetches news from the NY Times API and processes it correctly', function () {
    Http::fake([
        'https://api.nytimes.com/svc/topstories/v2/home.json*' => Http::response([
            'results' => [
                [
                    'title' => 'Breaking NY Times News',
                    'abstract' => 'A brief description of breaking news.',
                    'url' => 'https://nytimes.com/breaking-news',
                    'author' => 'John NY',
                    'item_type' => 'Article',
                    'section' => 'World',
                    'published_date' => '2024-12-14T10:00:00Z',
                ],
                [
                    'title' => 'Another NY Times Article',
                    'abstract' => 'Another description.',
                    'url' => 'https://nytimes.com/another-article',
                    'author' => 'Jane NY',
                    'item_type' => 'Opinion',
                    'section' => 'Politics',
                    'published_date' => '2024-12-14T12:00:00Z',
                ],
            ],
        ], 200),
    ]);

    // Act: Fetch news using the provider
    $provider = new NyTimesProvider();
    $news = $provider->fetchNews();

    // Assert: Validate fetched articles
    $articles = $news->all();

    expect($articles)->toHaveCount(2);

    /** @var NewsDto $firstArticle */
    $firstArticle = $articles[0];
    expect($firstArticle->title)->toBe('Breaking NY Times News')
        ->and($firstArticle->author)->toBe('John NY')
        ->and($firstArticle->sourceName)->toBe('World')
        ->and($firstArticle->publishedAt)->toBeInstanceOf(Carbon\Carbon::class);

    /** @var NewsDto $secondArticle */
    $secondArticle = $articles[1];
    expect($secondArticle->title)->toBe('Another NY Times Article')
        ->and($secondArticle->author)->toBe('Jane NY')
        ->and($secondArticle->sourceName)->toBe('Politics')
        ->and($secondArticle->publishedAt)->toBeInstanceOf(Carbon\Carbon::class);
});

it('handles NY Times API failure gracefully', function () {
    Http::fake([
        'https://api.nytimes.com/svc/topstories/v2/home.json*' => Http::response(null, 500),
    ]);

    // Act & Assert: Expect an exception
    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Failed to fetch news from New York Times.');

    $provider = new NyTimesProvider();
    $provider->fetchNews();
});

it('fetches and processes large NY Times responses efficiently', function () {
    // Arrange: Mock a large response
    $mockArticles = [];
    for ($i = 0; $i < 1000; $i++) {
        $mockArticles[] = [
            'title' => "NY Times Article $i",
            'abstract' => "Description $i",
            'url' => "https://nytimes.com/news-$i",
            'author' => "Author $i",
            'item_type' => 'Article',
            'section' => "Section $i",
            'published_date' => now()->toIso8601String(),
        ];
    }

    Http::fake([
        'https://api.nytimes.com/svc/topstories/v2/home.json*' => Http::response(['results' => $mockArticles], 200),
    ]);

    // Act: Fetch news using the provider
    $provider = new NyTimesProvider();
    $news = $provider->fetchNews();

    // Assert: Validate lazy processing
    expect($news)->toBeInstanceOf(LazyCollection::class);
    $articles = $news->take(10)->all();

    expect($articles)->toHaveCount(10)
        ->and($articles[0]->title)->toBe('NY Times Article 0')
        ->and($articles[9]->title)->toBe('NY Times Article 9');
});
