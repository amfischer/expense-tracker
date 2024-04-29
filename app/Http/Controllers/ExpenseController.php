<?php

namespace App\Http\Controllers;

use App\Enums\Currency;
use App\Enums\PaymentMethod;
use App\Http\Requests\ExpenseRequest;
use App\Models\Expense;
use App\Models\Receipt;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExpenseController extends Controller
{
    public function index(Request $request): Response
    {
        $validator = Validator::make($request->all(['query', 'category_ids', 'sort_by', 'payment_methods']), [
            'query'             => 'nullable',
            'category_ids'      => 'nullable|array',
            'sort_by'           => 'nullable',
            'payment_methods'   => 'nullable|array',
            'payment_methods.*' => Rule::in(PaymentMethod::values()),
        ]);

        // TODO - doesn't return an Inertia response, throws an error for some reason.
        // test by forcing validation to fail.
        if ($validator->fails()) {
            return back()->withErrors($validator->errors()->messages());
        }

        $validated = $validator->valid();

        $query = Expense::search($validated['query'])
            ->where('user_id', $request->user()->id)
            ->query(fn (Builder $query) => $query->with(['category']));

        if ($validated['category_ids']) {
            $query->whereIn('category_id', $validated['category_ids']);
        }

        if ($validated['payment_methods']) {
            $query->whereIn('payment_method', $validated['payment_methods']);
        }

        $query->orderBy($validated['sort_by'] ?? 'effective_date', 'desc');

        $expenses = $query->paginate(10)->appends(Arr::whereNotNull($validated));

        $categories = $request->user()->categories;
        $paymentMethods = PaymentMethod::HTMLSelectOptions();

        return Inertia::render('Expenses/Index', compact('expenses', 'categories', 'paymentMethods'));
    }

    public function create(Request $request): Response
    {
        $user = $request->user();

        $categories = $user->categoriesArray;
        $currencies = Currency::HTMLSelectOptions();
        $paymentMethods = PaymentMethod::HTMLSelectOptions();

        return Inertia::render('Expenses/Create', compact('categories', 'currencies', 'paymentMethods'));
    }

    public function store(ExpenseRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $request->user()->expenses()->create($validated);

        return back()->with('message', 'Expense successfully created.');
    }

    public function edit(Expense $expense): Response
    {
        Gate::authorize('view', $expense);

        $categories = $expense->user->categoriesArray;
        $currencies = Currency::HTMLSelectOptions();
        $paymentMethods = PaymentMethod::HTMLSelectOptions();

        $receipt = $expense->receipts()->first();

        return Inertia::render('Expenses/Edit', compact('expense', 'categories', 'currencies', 'paymentMethods', 'receipt'));
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

        return back()->with('message', 'Expense successfully updated.');
    }

    public function delete(Expense $expense): RedirectResponse
    {
        Gate::authorize('delete', $expense);

        $expense->delete();

        return redirect()->route('expenses.index')->with('message', 'Expense successfully deleted.');
    }

    /**
     * Receipt Endpoints
     */
    public function storeReceipt(Request $request, Expense $expense): RedirectResponse
    {
        Gate::authorize('update', $expense);

        $validated = $request->validate([
            'receipt_upload' => ['required', File::types(['png', 'jpg', 'jpeg', 'webp', 'pdf'])->min('1kb')->max('1mb')],
        ]);

        /** @var \Illuminate\Http\UploadedFile $file */
        $file = $validated['receipt_upload'];

        $storagePath = $expense->getReceiptStoragePath();
        $filename = $file->hashName();

        Storage::disk('receipts')->putFileAs($storagePath, $file, $filename);

        Receipt::create([
            'user_id'    => Auth::user()->id,
            'expense_id' => $expense->id,
            'filename'   => $filename,
            'mimetype'   => $file->getMimeType(),
            'size'       => $file->getSize(),
        ]);

        return back()->with('message', 'Receipt successfully uploaded.');
    }

    public function getReceiptContents(Expense $expense, Receipt $receipt): BinaryFileResponse
    {
        Gate::authorize('view', $expense);

        $path = Storage::disk('receipts')->path($expense->getReceiptStoragePath() . '/' . $receipt->filename);

        return response()->file($path);
    }

    public function deleteReceipt(Expense $expense, Receipt $receipt): RedirectResponse
    {
        Gate::authorize('delete', $expense);

        Storage::disk('receipts')->delete($expense->getReceiptStoragePath() . '/' . $receipt->filename);

        $receipt->delete();

        return back()->with('message', 'Receipt successfully deleted');
    }
}
