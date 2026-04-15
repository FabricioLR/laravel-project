<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $todos = $request->user()->todos()
            ->when($request->status, function ($query, $status) {
                if ($status === 'completed') {
                    $query->where('is_completed', true);
                } elseif ($status === 'pending') {
                    $query->where('is_completed', false);
                }
            })
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        return \Inertia\Inertia::render('dashboard', [
            'todos' => $todos,
            'filters' => $request->only(['status', 'search']),
        ]);
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
    public function update(\App\Http\Requests\TodoRequest $request, Todo $todo)
    {
        \Illuminate\Support\Facades\Gate::authorize('update', $todo);

        $todo->update($request->validated());

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo)
    {
        \Illuminate\Support\Facades\Gate::authorize('delete', $todo);

        $todo->delete();

        return redirect()->back();
    }
}
