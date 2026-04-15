<?php

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('a user can see only their own todos', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    Todo::factory()->create(['user_id' => $user1->id, 'title' => 'User 1 Task']);
    Todo::factory()->create(['user_id' => $user2->id, 'title' => 'User 2 Task']);

    $this->actingAs($user1)
        ->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('dashboard')
            ->has('todos', 1)
            ->where('todos.0.title', 'User 1 Task')
        );
});

test('it can filter by completed status', function () {
    $user = User::factory()->create();
    Todo::factory()->create(['user_id' => $user->id, 'is_completed' => true, 'title' => 'Completed Task']);
    Todo::factory()->create(['user_id' => $user->id, 'is_completed' => false, 'title' => 'Pending Task']);

    $this->actingAs($user)
        ->get(route('dashboard', ['status' => 'completed']))
        ->assertInertia(fn (Assert $page) => $page
            ->has('todos', 1)
            ->where('todos.0.title', 'Completed Task')
        );
});

test('it can filter by pending status', function () {
    $user = User::factory()->create();
    Todo::factory()->create(['user_id' => $user->id, 'is_completed' => true, 'title' => 'Completed Task']);
    Todo::factory()->create(['user_id' => $user->id, 'is_completed' => false, 'title' => 'Pending Task']);

    $this->actingAs($user)
        ->get(route('dashboard', ['status' => 'pending']))
        ->assertInertia(fn (Assert $page) => $page
            ->has('todos', 1)
            ->where('todos.0.title', 'Pending Task')
        );
});

test('it can search by title', function () {
    $user = User::factory()->create();
    Todo::factory()->create(['user_id' => $user->id, 'title' => 'Buy milk']);
    Todo::factory()->create(['user_id' => $user->id, 'title' => 'Call mom']);

    $this->actingAs($user)
        ->get(route('dashboard', ['search' => 'milk']))
        ->assertInertia(fn (Assert $page) => $page
            ->has('todos', 1)
            ->where('todos.0.title', 'Buy milk')
        );
});
