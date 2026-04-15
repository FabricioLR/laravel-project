<?php

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a todo can be persisted and retrieved', function () {
    $user = User::factory()->create();

    $todoData = [
        'user_id' => $user->id,
        'title' => 'Test Task',
        'is_completed' => true,
    ];

    $todo = Todo::create($todoData);

    $this->assertDatabaseHas('todos', $todoData);
    expect($todo->title)->toBe('Test Task');
    expect($todo->is_completed)->toBeTrue();
});

test('is_completed defaults to false', function () {
    $user = User::factory()->create();

    $todo = Todo::create([
        'user_id' => $user->id,
        'title' => 'Incomplete Task',
    ]);

    $todo->refresh();

    expect($todo->is_completed)->toBeFalse();
    $this->assertDatabaseHas('todos', [
        'id' => $todo->id,
        'is_completed' => false,
    ]);
});

test('it throws an error if user_id is missing', function () {
    $this->expectException(\Illuminate\Database\QueryException::class);

    Todo::create([
        'title' => 'Orphaned Task',
    ]);
});
