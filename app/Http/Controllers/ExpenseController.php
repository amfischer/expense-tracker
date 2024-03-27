<?php

namespace App\Http\Controllers;

use App\Enums\Currency;
use App\Http\Requests\ExpenseRequest;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseController extends Controller
{
    public function index(Request $request): Response
    {
        $validator = Validator::make($request->all(['query', 'category_ids']), [
            'query'        => 'nullable',
            'category_ids' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors()->messages());
        }

        $validated = $validator->valid();

        $query = Expense::search($validated['query'])
            ->query(fn (Builder $query) => $query->with(['category', 'tags']));

        if ($validated['category_ids']) {
            $query->whereIn('category_id', $validated['category_ids']);
        }

        $expenses = $query->paginate(10)->appends(Arr::whereNotNull($validated));

        $categories = $request->user()->categories;

        return Inertia::render('Expenses/Index', compact('expenses', 'categories'));
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
        $currencies = Currency::values();

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
