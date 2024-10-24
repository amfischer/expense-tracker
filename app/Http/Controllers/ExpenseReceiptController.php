<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Receipt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExpenseReceiptController extends Controller
{
    public function show(Expense $expense, Receipt $receipt): BinaryFileResponse
    {
        Gate::authorize('view', $expense);

        $path = Storage::disk('receipts')->path($expense->getReceiptStoragePath() . '/' . $receipt->filename);

        return response()->file($path);
    }

    public function store(Request $request, Expense $expense): RedirectResponse
    {
        Gate::authorize('update', $expense);

        $validated = $request->validate([
            'receipt_upload' => ['required', File::types(['png', 'jpg', 'jpeg', 'webp', 'pdf'])->min('1kb')->max('2mb')],
        ]);

        /** @var \Illuminate\Http\UploadedFile $file */
        $file = $validated['receipt_upload'];

        $storagePath = $expense->getReceiptStoragePath();
        $filename = $file->hashName();

        Storage::disk('receipts')->putFileAs($storagePath, $file, $filename);

        $receipt = $expense->receipts()->create([
            'user_id'  => $request->user()->id,
            'filename' => $filename,
            'mimetype' => $file->getMimeType(),
            'size'     => $file->getSize(),
        ]);

        return back()->withInput(compact('receipt'))->with('message', 'Receipt successfully uploaded.');
    }

    public function destroy(Request $request, Expense $expense, Receipt $receipt): RedirectResponse
    {
        Gate::authorize('delete', $expense);

        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        Storage::disk('receipts')->delete($expense->getReceiptStoragePath() . '/' . $receipt->filename);

        $receipt->delete();

        return back()->with('message', 'Receipt successfully deleted');
    }
}
