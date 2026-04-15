<?php

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a user has many todos', function () {
    $user = User::factory()->create();
    $todo1 = Todo::factory()->create(['user_id' => $user->id]);
    $todo2 = Todo::factory()->create(['user_id' => $user->id]);

    expect($user->todos)->toHaveCount(2);
    expect($user->todos->first())->toBeInstanceOf(Todo::class);
    expect($user->todos->contains($todo1))->toBeTrue();
    expect($user->todos->contains($todo2))->toBeTrue();
});

test('a todo belongs to a user', function () {
    $user = User::factory()->create();
    $todo = Todo::factory()->create(['user_id' => $user->id]);

    expect($todo->user)->toBeInstanceOf(User::class);
    expect($todo->user->id)->toBe($user->id);
});
