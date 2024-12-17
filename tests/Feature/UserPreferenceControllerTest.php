<?php

use App\Domains\News\Actions\GetUserPreferenceArticleAction;
use App\Domains\News\Models\UserPreference;
use App\Models\User;

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
    // Arrange: Create a user and preferences
    $user = User::factory()->create();
    UserPreference::factory()->create([
        'user_id' => $user->id,
        'categories' => ['technology'],
        'authors' => ['John Doe'],
        'sources' => ['TechCrunch'],
    ]);

    // Mock the personalized articles action
    $this->mock(GetUserPreferenceArticleAction::class, function ($mock) {
        $mock->shouldReceive('run')
            ->once()
            ->andReturn([
                [
                    'title' => 'Tech News Today',
                    'author' => 'John Doe',
                    'source' => 'TechCrunch',
                ],
            ]);
    });

    // Act: Send a GET request to fetch personalized articles
    $response = $this->actingAs($user)->getJson(route('preferences.personalized'));

    // Assert: Response should return personalized articles
    $response->assertOk()
        ->assertJson([
            [
                'title' => 'Tech News Today',
                'author' => 'John Doe',
                'source' => 'TechCrunch',
            ],
        ]);
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
