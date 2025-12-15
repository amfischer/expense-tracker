<?php

namespace App\Http\Controllers;

use App\Enums\Currency;
use App\Enums\PaymentMethod;
use App\Http\Requests\ExpenseRequest;
use App\Models\Expense;
use App\Rules\AlphaSpace;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseController extends Controller
{
    public function index(Request $request): Response|RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'query'                     => ['nullable', new AlphaSpace],
            'sort_by'                   => ['nullable', Rule::in(['effective_date', 'amount', 'payee'])],
            'sort_dir'                  => ['nullable', Rule::in(['asc', 'desc'])],
            'date'                      => 'nullable|array|size:2',
            'date.*'                    => 'date_format:Y-m-d',
            'filters'                   => 'nullable|array:category_ids,payment_methods,business_expenses',
            'filters.category_ids.*'    => [
                'numeric',
                Rule::exists('categories', 'id')->where(function (Builder $query) use ($request) {
                    return $query->where('user_id', $request->user()->id);
                })],
            'filters.payment_methods.*' => Rule::in(PaymentMethod::values()),
            'filters.business_expenses' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->setTargetUrl(route('expenses.index'))->withErrors($validator->errors()->messages(), 'scout');
        }

        $data = $validator->validate();

        $query = Expense::search($data['query'] ?? '')
            ->where('user_id', $request->user()->id)
            ->query(function (EloquentBuilder $query) use ($data) {
                $query->with(['category', 'receipts']);
                if ($data['date'] ?? false) {
                    $query->where('effective_date', '>=', Carbon::createFromFormat('Y-m-d', $data['date'][0])->startOfDay());
                    $query->where('effective_date', '<=', Carbon::createFromFormat('Y-m-d', $data['date'][1])->endOfDay());
                }
            });

        if ($data['filters']['category_ids'] ?? false) {
            $query->whereIn('category_id', $data['filters']['category_ids']);
        }

        if ($data['filters']['payment_methods'] ?? false) {
            $query->whereIn('payment_method', $data['filters']['payment_methods']);
        }

        if ($data['filters']['business_expenses'] ?? false) {
            $query->where('is_business_expense', 1);
        }

        if ($data['sort_by'] ?? false) {
            $query->orderBy($data['sort_by'], $data['sort_dir'] ?? 'asc');
        }

        $expenses = $query->paginate(15)->appends(Arr::whereNotNull($data));

        $categories = $request->user()->categories()->orderBy('name')->get();
        $paymentMethods = PaymentMethod::HTMLSelectOptions();
        $currencies = Currency::HTMLSelectOptions();

        return Inertia::render('Expenses/Index', compact('expenses', 'categories', 'paymentMethods', 'currencies'));
    }

    public function store(ExpenseRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $request->user()->expenses()->create($validated);

        return back()->with('message', 'Expense successfully created.')->with('title', 'Created!');
    }

    public function edit(Request $request, Expense $expense): Response
    {
        Gate::authorize('view', $expense);

        if (str_contains(url()->previous(), 'expenses') && ! str_contains(url()->previous(), '/edit')) {
            $request->session()->put('url.intended.expenses', url()->previous());
        }

        $categories = $expense->user->categories()->orderBy('name')->get();
        $currencies = Currency::HTMLSelectOptions();
        $paymentMethods = PaymentMethod::HTMLSelectOptions();

        $receipt = $expense->receipts()->first();

        return Inertia::render('Expenses/Edit', [
            'expense'        => $expense,
            'categories'     => $categories,
            'currencies'     => $currencies,
            'paymentMethods' => $paymentMethods,
            'receipt'        => $receipt,
        ]);
    }

    public function update(ExpenseRequest $request, Expense $expense): RedirectResponse
    {
        Gate::authorize('update', $expense);

        $validated = $request->validated();

        $transactionDateIsChanging = $expense->transaction_date->format('Y-m-d') !== $validated['transaction_date'];

        $updateReceiptPaths = $expense->receipts->isNotEmpty() && $transactionDateIsChanging;
        $oldStoragePath = $expense->getReceiptStoragePath();

        $expense->update($validated);

        if ($updateReceiptPaths) {
            $newStoragePath = $expense->refresh()->getReceiptStoragePath();

            foreach ($expense->receipts as $r) {
                Storage::disk('receipts')->move($oldStoragePath . '/' . $r->filename, $newStoragePath . '/' . $r->filename);
            }
        }

        $redirectUrl = $request->session()->pull('url.intended.expenses', route('expenses.index'));

        return redirect($redirectUrl)->with('message', 'Expense successfully updated.')->with('title', 'Updated!');
    }

    public function delete(Request $request, Expense $expense): RedirectResponse
    {
        Gate::authorize('delete', $expense);

        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        if ($expense->receipts()->count() > 0) {
            return back()->withErrors(['password' => 'Has attached receipts, cannot delete expense.']);
        }

        $expense->delete();

        return redirect()->route('expenses.index')->with('message', 'Expense successfully deleted.')->with('title', 'Deleted!');
    }
}
