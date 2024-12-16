<?php

use App\Models\User;

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertOk();

    expect($response->json())->not()->toBeEmpty()
        ->and($response->json())->toHaveKey('token');
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/api/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/api/logout');

    $this->assertGuest();
    $response->assertNoContent();
});
