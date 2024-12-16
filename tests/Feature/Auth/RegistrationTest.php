<?php

test('new users can register', function () {
    $response = $this->post('/api/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertCreated();

    expect($response->json())->not()->toBeEmpty()
        ->and($response->json())->toHaveKey('token');
});
