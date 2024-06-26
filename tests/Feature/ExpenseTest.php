<?php

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

it('can show expenses', function () {
    $user = User::factory()->hasAppAcess()->create();
    $expense = Expense::factory()->create(['user_id' => $user->id]);

    $this->get('/expenses')
        ->assertRedirect(route('login'));

    login($user);

    $this->get(route('expenses.index'))
        ->assertSee($expense->payee)
        ->assertSee($expense->total);
});

it("will not show another user's expenses", function () {
    $expenseRestricted = Expense::factory()->create();

    login();

    $user = Auth::user();

    $this->get(route('expenses.index'))
        ->assertOk()
        ->assertDontSee($expenseRestricted->payee)
        ->assertDontSee($expenseRestricted->total);

    $this->assertDatabaseMissing('expenses', [
        'user_id' => $user->id,
    ]);
});

test('users can create new expenses', function () {
    login();

    $user = Auth::user();

    $formData = Expense::factory()->make(['user_id' => $user->id])->toArray();

    $this->post(route('expenses.store'), $formData);

    // db formats money as integer (in cents)
    $formData['amount'] *= 100;
    $formData['foreign_currency_conversion_fee'] *= 100;

    // Expense::class includes $appends property to show custom attributes in JSON response,
    // but we don't want those extra attributes when testing database rows
    $dataWithoutAppendedFields = [
        'user_id'                         => $formData['user_id'],
        'category_id'                     => $formData['category_id'],
        'payee'                           => $formData['payee'],
        'amount'                          => $formData['amount'],
        'foreign_currency_conversion_fee' => $formData['foreign_currency_conversion_fee'],
        'transaction_date'                => $formData['transaction_date'],
        'effective_date'                  => $formData['effective_date'],
        'currency'                        => $formData['currency'],
    ];

    $this->assertDatabaseHas('expenses', $dataWithoutAppendedFields);

});

test('users can update existing expenses', function () {
    $user = login();

    $expense = Expense::factory()->create(['user_id' => $user->id]);

    $this->assertModelExists($expense);

    $formData = Expense::factory()->make(['user_id' => $user->id])->toArray();

    $this->patch(route('expenses.update', $expense), $formData);

    // db formats money as integer (in cents)
    $formData['amount'] *= 100;
    $formData['foreign_currency_conversion_fee'] *= 100;

    // Expense::class includes $appends property to show custom attributes in JSON response,
    // but we don't want those extra attributes when testing database rows
    $dataWithoutAppendedFields = [
        'user_id'                         => $formData['user_id'],
        'category_id'                     => $formData['category_id'],
        'payee'                           => $formData['payee'],
        'amount'                          => $formData['amount'],
        'foreign_currency_conversion_fee' => $formData['foreign_currency_conversion_fee'],
        'transaction_date'                => $formData['transaction_date'],
        'effective_date'                  => $formData['effective_date'],
        'currency'                        => $formData['currency'],
    ];

    $this->assertDatabaseHas('expenses', $dataWithoutAppendedFields);
});

test('users can delete existing expenses', function () {
    $user = login();

    $expense = Expense::factory()->create(['user_id' => $user->id]);

    $this->assertModelExists($expense);

    $this->delete(route('expenses.delete', $expense));

    $this->assertModelMissing($expense);
    $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
});

/**
 * AUTHORIZATION TESTS
 */
it('will return a 404 if user attempts to access expenses from other accounts', function () {
    login();

    $expenseRestricted = Expense::factory()->create();

    $this->get(route('expenses.edit', $expenseRestricted))
        ->assertNotFound();
});

it('will return a 403 if user attempts to update expenses from other accounts', function () {
    $user = login();

    $expenseRestricted = Expense::factory()->create();

    $formData = $expenseRestricted->toArray();

    // create user owned category to pass validation. Gate auth check happens right after validation.
    $formData['category_id'] = Category::factory()->create(['user_id' => $user->id])->id;

    $this->patch(route('expenses.update', $expenseRestricted), $formData)
        ->assertForbidden();
});

it('will return a 403 if user attempts to delete expenses from other accounts', function () {
    $user = login();

    $expenseRestricted = Expense::factory()->create();

    $formData = $expenseRestricted->toArray();

    // create user owned category to pass validation. authentication happens right after validation
    $formData['category_id'] = Category::factory()->create(['user_id' => $user->id])->id;

    $this->delete(route('expenses.delete', $expenseRestricted), $formData)
        ->assertForbidden();
});
