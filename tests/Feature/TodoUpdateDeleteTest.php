<?php

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a user can toggle a todo status', function () {
    $user = User::factory()->create();
    $todo = Todo::factory()->create(['user_id' => $user->id, 'is_completed' => false]);

    $response = $this->actingAs($user)
        ->patch(route('todos.update', $todo), [
            'is_completed' => true,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('todos', [
        'id' => $todo->id,
        'is_completed' => true,
    ]);
});

test('a user can edit a todo title', function () {
    $user = User::factory()->create();
    $todo = Todo::factory()->create(['user_id' => $user->id, 'title' => 'Old Title']);

    $response = $this->actingAs($user)
        ->patch(route('todos.update', $todo), [
            'title' => 'New Title',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('todos', [
        'id' => $todo->id,
        'title' => 'New Title',
    ]);
});

test('a user can delete a todo', function () {
    $user = User::factory()->create();
    $todo = Todo::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->delete(route('todos.destroy', $todo));

    $response->assertRedirect();
    $this->assertDatabaseMissing('todos', [
        'id' => $todo->id,
    ]);
});
