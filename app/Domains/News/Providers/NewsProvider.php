<?php

namespace App\Domains\News\Providers;

use Illuminate\Support\LazyCollection;

interface NewsProvider
{
    public function fetchNews(): LazyCollection;
}
