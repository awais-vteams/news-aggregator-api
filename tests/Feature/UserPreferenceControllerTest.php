<?php

use App\Domains\News\Models\Article;
use App\Domains\News\Models\UserPreference;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

it('retrieves user preferences successfully', function () {
    // Arrange: Create a user and user preferences
    $user = User::factory()->create();
    $preferences = UserPreference::factory()->create([
        'user_id' => $user->id,
        'categories' => ['technology', 'health'],
        'authors' => ['John Doe', 'Jane Smith'],
        'sources' => ['TechCrunch', 'NYTimes'],
    ]);

    // Act: Send a GET request to fetch preferences
    $response = $this->actingAs($user)->getJson(route('preferences.set'));

    // Assert: Response should include preferences
    $response->assertOk()
        ->assertJson([
            'data' => [
                'categories' => $preferences->categories,
                'authors' => $preferences->authors,
                'sources' => $preferences->sources,
            ],
        ]);
});

it('returns empty data if user preferences do not exist', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson(route('preferences.set'));

    $response->assertOk()
        ->assertJson([
            'data' => [],
        ]);
});

it('sets user preferences successfully', function () {
    // Arrange: Create a user
    $user = User::factory()->create();

    $data = [
        'categories' => ['sports', 'business'],
        'authors' => ['Alice Johnson', 'Bob Brown'],
        'sources' => ['CNN', 'BBC'],
    ];

    // Act: Send a POST request to set preferences
    $response = $this->actingAs($user)->postJson(route('preferences.set'), $data);

    // Assert: Response should have a 201 status and include preferences
    $response->assertCreated()
        ->assertJson([
            'data' => [
                'categories' => $data['categories'],
                'authors' => $data['authors'],
                'sources' => $data['sources'],
            ],
        ]);

    // Assert: Preferences are saved in the database
    $this->assertDatabaseHas('user_preferences', [
        'user_id' => $user->id,
        'categories' => json_encode($data['categories']),
        'authors' => json_encode($data['authors']),
        'sources' => json_encode($data['sources']),
    ]);
});

it('retrieves personalized articles based on user preferences', function () {
    // Arrange
    $user = User::factory()->create();
    $preferences = UserPreference::factory()->create([
        'user_id' => $user->id,
        'sources' => ['TechCrunch', 'NYTimes'],
        'categories' => ['technology', 'sports'],
        'authors' => ['John Doe', 'Jane Smith'],
    ]);

    // Create matching and non-matching articles
    $matchingArticle1 = Article::factory()->create([
        'source_name' => 'TechCrunch',
        'category' => 'technology',
        'author' => 'John Doe',
        'published_at' => now(),
    ]);

    $matchingArticle2 = Article::factory()->create([
        'source_name' => 'NYTimes',
        'category' => 'sports',
        'author' => 'Jane Smith',
        'published_at' => now(),
    ]);

    $nonMatchingArticle = Article::factory()->create([
        'source_name' => 'Another Source',
        'category' => 'health',
        'author' => 'Someone Else',
        'published_at' => now(),
    ]);

    // Clear cache to ensure fresh results
    Cache::forget('user_'.$user->id.'_personalized_articles');

    // Act
    $response = $this->actingAs($user)->get('/api/personalized-articles');

    // Assert
    $response->assertStatus(200);
    $response->assertJsonFragment(['source_name' => 'TechCrunch']);
    $response->assertJsonFragment(['source_name' => 'NYTimes']);
    $response->assertJsonMissing(['source_name' => 'Another Source']);

    $data = $response->json('data');

    // Check that the response only contains matching articles
    expect($data)->toHaveCount(2)
        ->and($data[0]['source_name'])->toBe('NYTimes')
        ->and($data[1]['source_name'])->toBe('TechCrunch');
});

it('returns 404 when fetching articles if preferences are not set', function () {
    // Arrange: Create a user without preferences
    $user = User::factory()->create();

    // Act: Send a GET request to fetch personalized articles
    $response = $this->actingAs($user)->getJson(route('preferences.personalized'));

    // Assert: Response should return 404
    $response->assertNotFound()
        ->assertJson([
            'message' => 'No preferences set.',
        ]);
});
