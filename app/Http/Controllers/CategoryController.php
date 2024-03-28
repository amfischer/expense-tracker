<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Rules\AlphaSpace;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $categories = Category::withCount('expenses')->where(['user_id' => $request->user()->id])->get();

        return Inertia::render('Categories/Index', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'  => ['required', new AlphaSpace, Rule::unique('categories')->where(fn (Builder $query) => $query->where('user_id', $user->id))],
            'color' => ['required', 'hex_color'],
        ]);

        $request->user()->categories()->create($validated);

        return back()->with('message', 'Category successfully created.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        Gate::authorize('update', $category);

        $user = Auth::user();

        $validated = $request->validate([
            'name'  => ['required', new AlphaSpace, Rule::unique('categories')->where(fn (Builder $query) => $query->where('user_id', $user->id))->ignore($category->id)],
            'color' => ['required', 'hex_color'],
        ]);

        $category->update($validated);

        return back()->with('message', 'Category successfully updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Category $category): RedirectResponse
    {
        Gate::authorize('delete', $category);

        if ($category->name === Category::DEFAULT) {
            return back()->withErrors(['message' => 'Default category cannot be deleted.']);
        }

        $category->loadCount('expenses');

        if ($category->expenses_count !== 0) {
            $count = $category->expenses_count;

            return back()->withErrors(['message' => 'category is linked to '.$count.' expenses. Remove these relationships before deleting.']);
        }

        $category->delete();

        return redirect()->route('categories.index')->with('message', 'Category successfully deleted.');
    }
}
