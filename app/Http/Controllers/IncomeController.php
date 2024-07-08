<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Rules\AlphaSpace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IncomeController extends Controller
{
    public function index(Request $request): Response|RedirectResponse
    {
        $incomes = Income::paginate(15);

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

    public function edit(Request $request, Income $income): Response|RedirectResponse
    {
        // code...
    }

    public function update(Request $request): Response|RedirectResponse
    {
        // code...
    }

    public function delete(Request $request): Response|RedirectResponse
    {
        // code...
    }
}
