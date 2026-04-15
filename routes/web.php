<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('dashboard', \App\Http\Controllers\TodoController::class)
        ->parameter('dashboard', 'todo')
        ->names([
            'index' => 'dashboard',
            'store' => 'todos.store',
            'update' => 'todos.update',
            'destroy' => 'todos.destroy',
        ]);
});

require __DIR__.'/settings.php';
