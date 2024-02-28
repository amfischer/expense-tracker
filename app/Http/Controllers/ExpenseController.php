<?php

namespace App\Http\Controllers;

use App\Enums\Currency;
use App\Http\Requests\ExpenseRequest;
use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseController extends Controller
{
    public function index(Request $request): Response
    {
        $expenses = $request->user()->expenses;
        $expenses->load(['category', 'tags']);

        return Inertia::render('Expenses/Table', compact('expenses'));
    }

    public function create(Request $request): Response
    {
        $user = $request->user();

        $categories = $user->categoriesArray;
        $tags = $user->tagsArray;
        $currencies = Currency::values();

        return Inertia::render('Expenses/Create', compact('categories', 'tags', 'currencies'));
    }

    public function store(ExpenseRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // get tags & separate from Expense payload
        $tags = [];

        if (isset($validated['tags'])) {
            $tags = $validated['tags'];
            unset($validated['tags']);
        }

        // create expense
        $expense = $request->user()->expenses()->create($validated);

        // create tag relationships
        foreach ($tags as $tagId) {
            $expense->tags()->attach($tagId);
        }

        return back()->with('message', 'Expense successfully created.');
    }

    public function edit(Expense $expense): Response
    {
        Gate::authorize('view', $expense);

        $categories = $expense->user->categoriesArray;
        $tags = $expense->user->tagsArray;
        $currencies = Currency::options();

        return Inertia::render('Expenses/Edit', compact('expense', 'categories', 'tags', 'currencies'));
    }

    public function update(ExpenseRequest $request, Expense $expense): RedirectResponse
    {
        Gate::authorize('update', $expense);

        $validated = $request->validated();

        // get tags & separate from Expense payload
        $tagIds = [];

        if (isset($validated['tags'])) {
            $tagIds = $validated['tags'];
            unset($validated['tags']);
        }

        $expense->update($validated);

        $expense->tags()->detach();

        $expense->tags()->attach($tagIds);

        return back()->with('message', 'Expense successfully updated.');
    }

    public function delete(Expense $expense): RedirectResponse
    {
        Gate::authorize('delete', $expense);

        // remove tags & delete
        $expense->tags()->detach();

        $expense->delete();

        return redirect()->route('expenses.index')->with('message', 'Expense successfully deleted.');
    }
}
