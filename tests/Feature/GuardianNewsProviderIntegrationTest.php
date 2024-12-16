<?php

use App\Domains\News\DTO\NewsDto;
use App\Domains\News\Providers\GuardianNewsProvider;
use Illuminate\Support\Facades\Http;

it('fetches news from The Guardian API', function () {
    $provider = new GuardianNewsProvider();

    $newsCollection = $provider->fetchNews();

    $newsArray = $newsCollection->take(5)->all();

    expect($newsArray)->not()->toBeEmpty()
        ->and($newsArray[0])->toBeInstanceOf(App\Domains\News\DTO\NewsDto::class);
});

it('throws an exception when the API request fails', function () {
    Http::fake([
        'https://content.guardianapis.com/search*' => Http::response([], 500), // Mock failure
    ]);

    $provider = new GuardianNewsProvider();

    expect(fn() => $provider->fetchNews()->all())
        ->toThrow(Exception::class, 'Failed to fetch news from The Guardian.');
});

it('handles missing fields in API response gracefully', function () {
    $mockResponse = [
        'response' => [
            'results' => [
                [
                    'webTitle' => 'Article with missing fields',
                    'webUrl' => 'https://example.com/article-missing-fields',
                    // Missing 'fields' and 'pillarName'
                    'webPublicationDate' => '2024-12-14T10:00:00Z',
                ],
            ],
        ],
    ];

    Http::fake([
        'https://content.guardianapis.com/search*' => Http::response($mockResponse, 200),
    ]);

    $provider = new GuardianNewsProvider();

    $newsArray = $provider->fetchNews()->all();

    /** @var NewsDto $article */
    $article = $newsArray[0];

    expect($article->title)->toBe('Article with missing fields')
        ->and($article->description)->toBeNull()
        ->and($article->category)->toBeNull();
});
