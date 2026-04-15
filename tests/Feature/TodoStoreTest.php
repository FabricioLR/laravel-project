<?php

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a user can create a todo', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('todos.store'), [
            'title' => 'My first todo',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('todos', [
        'user_id' => $user->id,
        'title' => 'My first todo',
        'is_completed' => false,
    ]);
});

test('title is required', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('todos.store'), [
            'title' => '',
        ]);

    $response->assertSessionHasErrors('title');
});

test('title must be at least 3 characters', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('todos.store'), [
            'title' => 'ab',
        ]);

    $response->assertSessionHasErrors('title');
});
