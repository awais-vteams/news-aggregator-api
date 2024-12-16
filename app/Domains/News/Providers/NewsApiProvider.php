<?php

namespace App\Domains\News\Providers;

use App\Domains\News\DTO\NewsDto;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\LazyCollection;

class NewsApiProvider implements NewsProvider
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.newsapi.key');
        $this->baseUrl = 'https://newsapi.org/v2';
    }

    public function fetchNews(): LazyCollection
    {
        $response = Http::get("{$this->baseUrl}/top-headlines", [
            'country' => 'us',
            'apiKey' => $this->apiKey,
        ]);

        if ($response->failed()) {
            throw new Exception('Failed to fetch news from NewsAPI.');
        }

        $articles = $response->json('articles', []);

        return LazyCollection::make(function () use ($articles) {
            foreach ($articles as $article) {
                yield new NewsDto(
                    title: $article['title'],
                    description: $article['description'] ?? null,
                    url: $article['url'],
                    author: $article['author'] ?? null,
                    content: $article['content'] ?? null,
                    category: 'top-headlines',
                    sourceName: $article['source']['name'] ?? null,
                    sourceUrl: null,
                    publishedAt: Carbon::parse($article['publishedAt'])
                );
            }
        });
    }
}
