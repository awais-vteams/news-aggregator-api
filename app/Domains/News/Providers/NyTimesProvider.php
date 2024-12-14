<?php

namespace App\Domains\News\Providers;

use App\Domains\News\DTO\NewsDto;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\LazyCollection;

class NyTimesProvider implements NewsProvider
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.nytimes.key');
        $this->baseUrl = 'https://api.nytimes.com/svc';
    }

    public function fetchNews(): LazyCollection
    {
        $response = Http::get("{$this->baseUrl}/topstories/v2/home.json", [
            'api-key' => $this->apiKey,
        ]);

        if ($response->failed()) {
            throw new Exception('Failed to fetch news from New York Times.');
        }

        $articles = $response->json('results', []);

        return LazyCollection::make(function () use ($articles) {
            foreach ($articles as $article) {
                yield new NewsDto(
                    title: $article['title'] ?? '',
                    description: $article['abstract'] ?? '',
                    url: $article['url'] ?? '',
                    author: $article['author'] ?? '',
                    content: $article['content'] ?? '',
                    category: '',
                    sourceName: $article['section'] ?? '',
                    publishedAt: $article['published_date'] ?? null
                );
            }
        });
    }
}
