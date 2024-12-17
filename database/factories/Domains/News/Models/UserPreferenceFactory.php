<?php

namespace Database\Factories\Domains\News\Models;

use App\Domains\News\Models\UserPreference;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserPreference>
 */
class UserPreferenceFactory extends Factory
{
    protected $model = UserPreference::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'categories' => $this->faker->randomElements(['technology', 'sports', 'health'], 2),
            'authors' => $this->faker->randomElements(['John Doe', 'Jane Smith'], 2),
            'sources' => $this->faker->randomElements(['NYTimes', 'TechCrunch'], 2),
        ];
    }
}
