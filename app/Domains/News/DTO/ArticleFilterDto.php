<?php

namespace App\Domains\News\DTO;

use DateTime;

class ArticleFilterDto
{
    public function __construct(
        public ?string $keyword = null,
        public ?DateTime $date = null,
        public ?string $category = null,
        public ?string $source = null
    ) {
    }
}
