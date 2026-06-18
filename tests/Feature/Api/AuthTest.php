<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('user can register and receives a token', function (): void {
    $response = $this->postJson('/api/register', [
        'name' => 'Giorgi Manager',
        'email' => 'giorgi@example.com',
        'password' => 'password123',
    ]);

    $response->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonStructure(['data' => ['user' => ['id', 'team'], 'token']]);

    $user = User::query()->where('email', 'giorgi@example.com')->firstOrFail();

    expect($user->team)->not->toBeNull()
        ->and($user->team->players)->toHaveCount(20);
});

it('user can login', function (): void {
    $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'login@example.com',
        'password' => 'password123',
    ])->assertCreated();

    $this->postJson('/api/login', [
        'email' => 'login@example.com',
        'password' => 'password123',
    ])->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonStructure(['data' => ['token']]);
});

it('user can logout', function (): void {
    $login = $this->postJson('/api/register', [
        'name' => 'Logout User',
        'email' => 'logout@example.com',
        'password' => 'password123',
    ])->json('data.token');

    $this->withToken($login)
        ->postJson('/api/logout')
        ->assertOk()
        ->assertJsonPath('success', true);
});

it('returns the authenticated user', function (): void {
    $token = $this->postJson('/api/register', [
        'name' => 'Me User',
        'email' => 'me@example.com',
        'password' => 'password123',
    ])->json('data.token');

    $this->withToken($token)
        ->getJson('/api/me')
        ->assertOk()
        ->assertJsonPath('data.email', 'me@example.com');
});
