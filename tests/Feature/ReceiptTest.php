<?php

use App\Models\Expense;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\patch;
use function Pest\Laravel\post;

beforeEach(function () {
    Storage::fake('receipts');
    $user = login();
    $this->expense = Expense::factory()->for($user)->create();
});

test('users can upload a receipt', function (UploadedFile $file) {

    post(route('expenses.receipts.store', $this->expense->id), ['receipt_upload' => $file])
        ->assertSessionDoesntHaveErrors()
        ->assertRedirect();

    assertDatabaseCount('receipts', 1);

    $receipt = $this->expense->receipts()->first();

    Storage::disk('receipts')->assertExists($receipt->filenameWithPath());

})->with([
    'png'  => UploadedFile::fake()->create('receipt.png', 500, 'image/png'),
    'jpeg' => UploadedFile::fake()->create('receipt.jpeg', 500, 'image/jpeg'),
    'jpg'  => UploadedFile::fake()->create('receipt.jpg', 500, 'image/jpeg'),
    'webp' => UploadedFile::fake()->create('receipt.webp', 500, 'image/webp'),
    'pdf'  => UploadedFile::fake()->create('receipt.pdf', 500, 'application/pdf'),
]);

test('prohibited file types return a validation error', function (UploadedFile $file) {

    post(route('expenses.receipts.store', $this->expense->id), ['receipt_upload' => $file])
        ->assertSessionHasErrors(['receipt_upload' => 'The receipt upload field must be a file of type: png, jpg, jpeg, webp, pdf.'])
        ->assertRedirect();

    assertDatabaseCount('receipts', 0);

    expect($this->expense->refresh()->receipts()->count())->toBe(0);
})->with([
    'png'  => UploadedFile::fake()->create('receipt.mp4', 500, 'application/mp4'),
    'jpeg' => UploadedFile::fake()->create('receipt.gif', 500, 'image/gif'),
    'jpg'  => UploadedFile::fake()->create('receipt.csv', 500, 'text/csv'),
    'webp' => UploadedFile::fake()->create('receipt.json', 500, 'application/json'),
    'pdf'  => UploadedFile::fake()->create('receipt.xml', 500, 'application/xml'),
]);

test('files cannot be bigger than 2MB', function (UploadedFile $file) {

    post(route('expenses.receipts.store', $this->expense->id), ['receipt_upload' => $file])
        ->assertSessionHasErrors(['receipt_upload' => 'The receipt upload field must be between 1 and 2000 kilobytes.'])
        ->assertRedirect();
})->with([
    'png'  => UploadedFile::fake()->create('receipt.png', 2001, 'image/png'),
    'jpeg' => UploadedFile::fake()->create('receipt.jpeg', 5000, 'image/jpeg'),
    'jpg'  => UploadedFile::fake()->create('receipt.jpg', 2010, 'image/jpeg'),
    'webp' => UploadedFile::fake()->create('receipt.webp', 3000, 'image/webp'),
    'pdf'  => UploadedFile::fake()->create('receipt.pdf', 4000, 'application/pdf'),
]);

test('users can delete a receipt', function () {

    post(route('expenses.receipts.store', $this->expense->id), [
        'receipt_upload' => UploadedFile::fake()->image('photo1.jpg')->size(200),
    ]);

    assertDatabaseCount('receipts', 1);

    $receipt = $this->expense->receipts()->first();

    Storage::disk('receipts')->assertExists($receipt->filenameWithPath());

    delete(route('expenses.receipts.delete', [$this->expense->id, $receipt->id]), ['password' => 'password'])
        ->assertSessionDoesntHaveErrors()
        ->assertRedirect();

    assertDatabaseCount('receipts', 0);

    Storage::disk('receipts')->assertMissing($receipt->filenameWithPath());
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

test('users can upload multiple receipts to the same expense', function () {
    post(route('expenses.receipts.store', $this->expense->id), [
        'receipt_upload' => UploadedFile::fake()->image('receipt1.jpg')->size(200),
    ])->assertRedirect();

    post(route('expenses.receipts.store', $this->expense->id), [
        'receipt_upload' => UploadedFile::fake()->create('receipt2.pdf', 500, 'application/pdf'),
    ])->assertRedirect();

    assertDatabaseCount('receipts', 2);

    $receipts = $this->expense->receipts()->with('expense')->get();
    expect($receipts)->toHaveCount(2);

    foreach ($receipts as $receipt) {
        Storage::disk('receipts')->assertExists($receipt->filenameWithPath());
    }
});

test('deleting one receipt does not affect other receipts', function () {
    post(route('expenses.receipts.store', $this->expense->id), [
        'receipt_upload' => UploadedFile::fake()->image('receipt1.jpg')->size(200),
    ]);

    post(route('expenses.receipts.store', $this->expense->id), [
        'receipt_upload' => UploadedFile::fake()->create('receipt2.pdf', 500, 'application/pdf'),
    ]);

    assertDatabaseCount('receipts', 2);

    $receipts = $this->expense->receipts()->with('expense')->get();
    $receiptToDelete = $receipts->first();
    $receiptToKeep = $receipts->last();

    delete(route('expenses.receipts.delete', [$this->expense->id, $receiptToDelete->id]), ['password' => 'password'])
        ->assertRedirect();

    assertDatabaseCount('receipts', 1);
    Storage::disk('receipts')->assertMissing($receiptToDelete->filenameWithPath());
    Storage::disk('receipts')->assertExists($receiptToKeep->filenameWithPath());
});

test('edit page returns all receipts', function () {
    post(route('expenses.receipts.store', $this->expense->id), [
        'receipt_upload' => UploadedFile::fake()->image('receipt1.jpg')->size(200),
    ]);

    post(route('expenses.receipts.store', $this->expense->id), [
        'receipt_upload' => UploadedFile::fake()->create('receipt2.pdf', 500, 'application/pdf'),
    ]);

    get(route('expenses.edit', $this->expense->id))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Expenses/Edit')
            ->has('expense.receipts', 2)
        );
});
