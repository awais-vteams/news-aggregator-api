<?php

namespace App\Domains\News\Providers;

use App\Domains\News\DTO\NewsDto;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\LazyCollection;

class GuardianNewsProvider implements NewsProvider
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.guardian.key');
        $this->baseUrl = 'https://content.guardianapis.com';
    }

    public function fetchNews(): LazyCollection
    {
        $response = Http::get("{$this->baseUrl}/search", [
            'api-key' => $this->apiKey,
            'section' => 'world',
        ]);

        if ($response->failed()) {
            throw new Exception('Failed to fetch news from The Guardian.');
        }

        $articles = $response->json('response.results', []);

        return LazyCollection::make(function () use ($articles) {
            foreach ($articles as $article) {
                yield new NewsDto(
                    title: $article['webTitle'],
                    description: $article['fields']['trailText'] ?? '',
                    url: $article['webUrl'],
                    author: $article['author'] ?? '',
                    content: $article['fields']['bodyText'] ?? '',
                    category: 'general',
                    sourceName: 'The Guardian',
                    publishedAt: $article['webPublicationDate'] ?? null
                );
            }
        });
    }
}
