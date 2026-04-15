<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\TodoRequest $request)
    {
        $request->user()->todos()->create($request->validated());

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo)
    {
        \Illuminate\Support\Facades\Gate::authorize('view', $todo);

        // TODO: Implement show logic
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Todo $todo)
    {
        \Illuminate\Support\Facades\Gate::authorize('update', $todo);

        // TODO: Implement update logic
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo)
    {
        \Illuminate\Support\Facades\Gate::authorize('delete', $todo);

        // TODO: Implement destroy logic
    }
}
