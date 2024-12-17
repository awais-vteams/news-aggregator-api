<?php

use App\Domains\News\Commands\FetchNews;
use Illuminate\Support\Facades\Schedule;

Schedule::command(FetchNews::class)->hourly();
