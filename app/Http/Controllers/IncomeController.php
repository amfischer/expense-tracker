<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Rules\AlphaSpace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class IncomeController extends Controller
{
    public function index(Request $request): Response|RedirectResponse
    {
        $incomes = $request->user()->incomes()->paginate(15);

        return Inertia::render('Incomes/Index', compact('incomes'));
    }

    public function create(): Response
    {
        return Inertia::render('Incomes/Create');
    }

    public function store(Request $request): Response|RedirectResponse
    {
        $data = $request->validate([
            'source'         => ['required', new AlphaSpace],
            'amount'         => 'required|decimal:0,2',
            'payment_date'   => 'required|date_format:Y-m-d',
            'effective_date' => 'required|date_format:Y-m-d',
            'notes'          => 'nullable',
        ]);

        $request->user()->incomes()->create($data);

        return back()->with('message', 'Income successfully created.');
    }

    public function edit(Income $income): Response
    {
        Gate::authorize('view', $income);

        return Inertia::render('Incomes/Edit', compact('income'));
    }

    public function update(Request $request, Income $income): Response|RedirectResponse
    {
        Gate::authorize('update', $income);

        $data = $request->validate([
            'source'         => ['required', new AlphaSpace],
            'amount'         => 'required|decimal:0,2',
            'payment_date'   => 'required|date_format:Y-m-d',
            'effective_date' => 'required|date_format:Y-m-d',
            'notes'          => 'nullable',
        ]);

        $income->update($data);

        return back()->with('message', 'Income successfully updated.');
    }

    public function delete(Request $request): Response|RedirectResponse
    {
        // code...
    }
}
