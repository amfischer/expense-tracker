<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $categories = Category::grouped($request->user());

        return Inertia::render('Categories/Index', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $request->user()->categories()->create($request->validated());

        return back()->with('message', 'Category successfully created.')->with('title', 'Created!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return back()->with('message', 'Category successfully updated.')->with('title', 'Updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Category $category): RedirectResponse
    {
        Gate::authorize('delete', $category);

        if ($category->name === Category::DEFAULT_NAME) {
            return back()->withErrors(['message' => 'Default category cannot be deleted.']);
        }

        $category->loadCount(['expenses', 'children']);

        if ($category->children_count !== 0) {
            return back()->withErrors(['message' => 'Category has ' . $category->children_count . ' subcategories. Remove them before deleting.']);
        }

        if ($category->expenses_count !== 0) {
            return back()->withErrors(['message' => 'Category is linked to ' . $category->expenses_count . ' expenses. Remove these relationships before deleting.']);
        }

        $category->delete();

        return redirect()->route('categories.index')->with('message', 'Category successfully deleted.')->with('title', 'Deleted!');
    }
}
