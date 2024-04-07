<?php

use App\Models\Expense;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\delete;
use function Pest\Laravel\patch;
use function Pest\Laravel\post;

beforeEach(function () {
    Storage::fake('receipts');
    $this->user = login();
    $this->expense = Expense::factory()->create(['user_id' => $this->user->id]);
});

test('users can upload a receipt', function () {

    post(route('expenses.receipts.store', $this->expense->id), [
        'receipt_upload' => UploadedFile::fake()->image('photo1.jpg')->size(500),
    ])
        ->assertSessionDoesntHaveErrors()
        ->assertRedirect();

    assertDatabaseCount('receipts', 1);

    $receipt = $this->expense->receipts()->first();

    Storage::disk('receipts')->assertExists($this->expense->getReceiptStoragePath() . '/' . $receipt->filename);

});

test('only image files are allowed to be uploaded', function () {

    post(route('expenses.receipts.store', $this->expense->id), [
        'receipt_upload' => UploadedFile::fake()->create('document.pdf')->size(500),
    ])
        ->assertSessionHasErrors(['receipt_upload' => 'The receipt upload field must be an image.'])
        ->assertRedirect();
});

test('image files cannot be bigger than 1MB', function () {

    post(route('expenses.receipts.store', $this->expense->id), [
        'receipt_upload' => UploadedFile::fake()->image('photo1.png')->size(1001),
    ])
        ->assertSessionHasErrors(['receipt_upload' => 'The receipt upload field must be between 1 and 1000 kilobytes.'])
        ->assertRedirect();
});

test('users can delete a receipt', function () {

    post(route('expenses.receipts.store', $this->expense->id), [
        'receipt_upload' => UploadedFile::fake()->image('photo1.jpg')->size(200),
    ]);

    assertDatabaseCount('receipts', 1);

    $receipt = $this->expense->receipts()->first();

    Storage::disk('receipts')->assertExists($this->expense->getReceiptStoragePath() . '/' . $receipt->filename);

    delete(route('expenses.receipts.delete', [$this->expense->id, $receipt->id]))
        ->assertSessionDoesntHaveErrors()
        ->assertRedirect();

    assertDatabaseCount('receipts', 0);

    Storage::disk('receipts')->assertMissing($this->expense->getReceiptStoragePath() . '/' . $receipt->filename);
});

test('updating the expense transaction date will move the corresponding receipt file', function () {
    post(route('expenses.receipts.store', $this->expense->id), [
        'receipt_upload' => UploadedFile::fake()->image('photo1.jpg')->size(200),
    ]);

    $receipt = $this->expense->receipts()->first();

    $oldStoragePath = $this->expense->getReceiptStoragePath();
    Storage::disk('receipts')->assertExists($oldStoragePath . '/' . $receipt->filename);

    $updateData = $this->expense->toArray();
    $updateData['transaction_date'] = '2000-01-01';

    patch(route('expenses.update', $this->expense->id), $updateData);

    $newStoragePath = $this->expense->refresh()->getReceiptStoragePath();
    Storage::disk('receipts')->assertExists($newStoragePath . '/' . $receipt->filename);

    expect($oldStoragePath)->not->toEqual($newStoragePath);
});
