<?php

namespace App\Domains\News\Commands;

use App\Domains\News\Actions\SaveArticleAction;
use App\Domains\News\Providers\GuardianNewsProvider;
use App\Domains\News\Providers\NewsApiProvider;
use App\Domains\News\Providers\NewsProvider;
use App\Domains\News\Providers\NyTimesProvider;
use Illuminate\Console\Command;

class FetchNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch news from different API sources';

    /**
     * Execute the console command.
     */
    public function handle(SaveArticleAction $saveArticleAction)
    {
        /** @var NewsProvider[] $newsProviders */
        $newsProviders = [
            new NewsApiProvider,
            new GuardianNewsProvider,
            new NyTimesProvider,
        ];

        foreach ($newsProviders as $provider) {
            $this->info('Fetching news from provider: '.class_basename($provider));

            $newsCollection = $provider->fetchNews();

            $saveArticleAction->run($newsCollection);

            $this->info('Successfully saved news from provider: '.class_basename($provider));
        }

        $this->info('News fetching completed.');
    }
}
