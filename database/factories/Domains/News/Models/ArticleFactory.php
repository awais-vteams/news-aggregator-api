<?php

namespace Database\Factories\Domains\News\Models;

use App\Domains\News\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'title' => fake()->name(),
            'description' => fake()->sentence,
            'url' => fake()->url(),
            'author' => fake()->name(),
            'content' => fake()->sentence(),
            'category' => fake()->word(),
            'source_name' => fake()->word(),
            'source_url' => fake()->url(),
            'published_at' => fake()->dateTime(),
        ];
    }
}
