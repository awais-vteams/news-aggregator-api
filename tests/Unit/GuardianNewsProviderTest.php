<?php

use App\Domains\News\Providers\GuardianNewsProvider;
use App\Domains\News\DTO\NewsDto;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

it('fetches news from The Guardian and transforms them into NewsDto objects', function () {
    // Mock the HTTP response
    $mockResponse = [
        'response' => [
            'results' => [
                [
                    'webTitle' => 'Test Article 1',
                    'webUrl' => 'https://example.com/article1',
                    'fields' => [
                        'trailText' => 'Short description of Article 1',
                        'bodyText' => 'Full content of Article 1',
                    ],
                    'pillarName' => 'News',
                    'webPublicationDate' => '2024-12-14T10:00:00Z',
                ],
                [
                    'webTitle' => 'Test Article 2',
                    'webUrl' => 'https://example.com/article2',
                    'fields' => [
                        'trailText' => 'Short description of Article 2',
                        'bodyText' => 'Full content of Article 2',
                    ],
                    'pillarName' => 'World',
                    'webPublicationDate' => '2024-12-13T15:30:00Z',
                ],
            ],
        ],
    ];

    Http::fake([
        'https://content.guardianapis.com/search*' => Http::response($mockResponse, 200),
    ]);

    // Instantiate the provider
    $provider = new GuardianNewsProvider();

    // Fetch news
    $newsCollection = $provider->fetchNews();

    $newsArray = $newsCollection->all();

    // Assertions
    expect($newsArray)->toHaveCount(2);

    /** @var NewsDto $firstArticle */
    $firstArticle = $newsArray[0];
    expect($firstArticle)->toBeInstanceOf(NewsDto::class)
        ->and($firstArticle->title)->toBe('Test Article 1')
        ->and($firstArticle->description)->toBe('Short description of Article 1')
        ->and($firstArticle->url)->toBe('https://example.com/article1')
        ->and($firstArticle->publishedAt)->toBeInstanceOf(Carbon::class)
        ->and($firstArticle->publishedAt->toDateTimeString())->toBe('2024-12-14 10:00:00');
});
