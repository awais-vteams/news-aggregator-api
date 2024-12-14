<?php

namespace App\Domains\News\DTO;

class NewsDto
{
    public function __construct(
        public string $title,
        public string $description,
        public string $url,
        public string $author,
        public string $content,
        public string $category,
        public string $sourceName,
        public ?string $publishedAt
    ) {
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'author' => $this->author,
            'content' => $this->content,
            'source_name' => $this->sourceName,
            'category' => $this->category,
            'published_at' => $this->publishedAt,
        ];
    }
}