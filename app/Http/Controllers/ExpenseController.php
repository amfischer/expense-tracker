<?php

namespace App\Http\Controllers;

use App\Enums\Currency;
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
use Illuminate\Validation\Rules\File;
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
            ->where('user_id', $request->user()->id)
            ->query(fn (Builder $query) => $query->with(['category']));

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
        $currencies = Currency::values();

        return Inertia::render('Expenses/Create', compact('categories', 'currencies'));
    }

    public function store(ExpenseRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // create expense
        $expense = $request->user()->expenses()->create($validated);

        return back()->with('message', 'Expense successfully created.');
    }

    public function edit(Expense $expense): Response
    {
        Gate::authorize('view', $expense);

        $categories = $expense->user->categoriesArray;
        $currencies = Currency::values();

        $receipt = $expense->receipts()->first();
        $receiptBase64Img = is_null($receipt) ? '' : base64_encode(Storage::disk('receipts')->get($receipt->file));

        return Inertia::render('Expenses/Edit', compact('expense', 'categories', 'currencies', 'receiptBase64Img'));
    }

    public function update(ExpenseRequest $request, Expense $expense): RedirectResponse
    {
        Gate::authorize('update', $expense);

        $validated = $request->validated();

        $expense->update($validated);

        return back()->with('message', 'Expense successfully updated.');
    }

    public function delete(Expense $expense): RedirectResponse
    {
        Gate::authorize('delete', $expense);

        $expense->delete();

        return redirect()->route('expenses.index')->with('message', 'Expense successfully deleted.');
    }

    public function storeReceipt(Request $request, Expense $expense): RedirectResponse
    {
        Gate::authorize('update', $expense);

        $validated = $request->validate([
            'receipt_upload' => ['required', File::image()->min('1kb')->max('1mb')],
        ]);

        /** @var \Illuminate\Http\UploadedFile $receipt */
        $receipt = $validated['receipt_upload'];

        $storagePath = $expense->getReceiptStoragePath();
        $filename = $receipt->hashName();

        Storage::disk('receipts')->putFileAs($storagePath, $receipt, $filename);

        Receipt::create([
            'user_id'    => Auth::user()->id,
            'expense_id' => $expense->id,
            'filename'   => $filename,
            'path'       => $storagePath,
            'mimetype'   => $receipt->getMimeType(),
            'size'       => $receipt->getSize(),
        ]);

        return back()->with('message', 'Receipt successfully uploaded.');
    }
}
