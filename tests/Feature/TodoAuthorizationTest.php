<?php

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a user can only view their own todo', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $todo = Todo::factory()->create(['user_id' => $user1->id]);

    $this->actingAs($user1)
        ->get(route('dashboard.show', $todo))
        ->assertSuccessful();

    $this->actingAs($user2)
        ->get(route('dashboard.show', $todo))
        ->assertForbidden();
});

test('a user cannot update another users todo', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $todo = Todo::factory()->create(['user_id' => $user1->id]);

    $this->actingAs($user2)
        ->patch(route('todos.update', $todo), ['is_completed' => true])
        ->assertForbidden();
});

test('a user cannot delete another users todo', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $todo = Todo::factory()->create(['user_id' => $user1->id]);

    $this->actingAs($user2)
        ->delete(route('todos.destroy', $todo))
        ->assertForbidden();
});
