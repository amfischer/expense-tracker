<?php

namespace App\Http\Controllers;

use App\Models\Income;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IncomeController extends Controller
{
    public function index(Request $request): Response|RedirectResponse
    {
        $incomes = Income::paginate(15);

        return Inertia::render('Income/Index', compact('incomes'));
    }

    public function create(Request $request)
    {
        // validation
    }

    public function store(Request $request)
    {
        // validation
    }

    public function edit(Request $request)
    {
        // code...
    }

    public function update(Request $request)
    {
        // code...
    }

    public function delete(Request $request)
    {
        // code...
    }
}
