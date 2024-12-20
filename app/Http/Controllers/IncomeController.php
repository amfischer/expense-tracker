<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Rules\AlphaSpace;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class IncomeController extends Controller
{
    public function index(Request $request): Response|RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'query'    => ['nullable', new AlphaSpace],
            'sort_by'  => ['nullable', Rule::in(['effective_date', 'amount', 'source'])],
            'sort_dir' => ['nullable', Rule::in(['asc', 'desc'])],
            'date'     => 'nullable|array|size:2',
            'date.*'   => 'date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return back()->setTargetUrl(route('incomes.index'))->withErrors($validator->errors()->messages(), 'scout');
        }

        $data = $validator->validate();

        $query = Income::search($data['query'] ?? '')
            ->where('user_id', $request->user()->id)
            ->query(function (EloquentBuilder $query) use ($data) {
                // $query->with(['documents']);
                if ($data['date'] ?? false) {
                    $query->where('effective_date', '>=', Carbon::createFromFormat('Y-m-d', $data['date'][0])->startOfDay());
                    $query->where('effective_date', '<=', Carbon::createFromFormat('Y-m-d', $data['date'][1])->endOfDay());
                }
            });

        if ($data['sort_by'] ?? false) {
            $query->orderBy($data['sort_by'], $data['sort_dir'] ?? 'asc');
        }

        $incomes = $query->paginate(15)->appends(Arr::whereNotNull($data));

        return Inertia::render('Incomes/Index', compact('incomes'));
    }

    public function store(Request $request): Response|RedirectResponse
    {
        $data = $request->validate([
            'source'           => ['required', new AlphaSpace],
            'amount'           => 'required|decimal:0,2',
            'payment_date'     => 'required|date_format:Y-m-d',
            'effective_date'   => 'required|date_format:Y-m-d',
            'is_earned_income' => 'required|boolean',
            'notes'            => 'nullable',
        ]);

        $request->user()->incomes()->create($data);

        return back()->with('message', 'Income successfully created.')->with('title', 'Created!');
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
            'source'           => ['required', new AlphaSpace],
            'amount'           => 'required|decimal:0,2',
            'payment_date'     => 'required|date_format:Y-m-d',
            'effective_date'   => 'required|date_format:Y-m-d',
            'is_earned_income' => 'required|boolean',
            'notes'            => 'nullable',
        ]);

        $income->update($data);

        return back()->with('message', 'Income successfully updated.')->with('title', 'Updated!');
    }

    public function delete(Request $request, Income $income): Response|RedirectResponse
    {
        Gate::authorize('delete', $income);

        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $income->delete();

        return redirect()->route('incomes.index')->with('message', 'Income successfully deleted.')->with('title', 'Deleted!');
    }
}
