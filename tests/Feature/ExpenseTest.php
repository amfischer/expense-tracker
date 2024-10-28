<?php

use App\Models\Category;
use App\Models\Expense;

it('can show expenses', function () {
    $user = login();

    $expense = Expense::factory()->create(['user_id' => $user->id]);

    $this->get(route('expenses.index'))
        ->assertSee($expense->payee)
        ->assertSee($expense->total);
});

it("will not show another user's expenses", function () {
    $user = login();

    $expenseRestricted = Expense::factory()->create();

    $this->get(route('expenses.index'))
        ->assertOk()
        ->assertDontSee($expenseRestricted->payee)
        ->assertDontSee($expenseRestricted->total);

    $this->assertDatabaseMissing('expenses', [
        'user_id' => $user->id,
    ]);
});

test('users can create new expenses', function () {
    $user = login();

    $payload = Expense::factory()->make(['user_id' => $user->id])->toArray();

    $this->post(route('expenses.store'), $payload);

    // db formats money as integer (in cents)
    $payload['amount'] *= 100;

    // Expense::class includes $appends property to show custom attributes in JSON response,
    // but we don't want those extra attributes when testing database rows
    unset($payload['amount_pretty'], $payload['effective_date_pretty'], $payload['has_receipt'], $payload['notes_raw']);

    $this->assertDatabaseHas('expenses', $payload);

});

test('users can update existing expenses', function () {
    $user = login();

    $expense = Expense::factory()->create(['user_id' => $user->id]);

    $payload = [
        'category_id'         => $expense->category->id,
        'payee'               => 'new payee',
        'amount'              => 201.56,
        'currency'            => 'USD',
        'is_business_expense' => true,
        'transaction_date'    => '2024-10-10',
        'effective_date'      => '2024-09-30',
    ];

    $this->patch(route('expenses.update', $expense), $payload);

    // db formats money as integer (in cents)
    $payload['amount'] *= 100;

    $this->assertDatabaseHas('expenses', $payload);

    $expense->refresh();

    expect($expense->payee)->toBe('new payee');
    expect($expense->amount)->toBe(20156);
    expect($expense->amount_pretty)->toBe('$201.56');
    expect($expense->is_business_expense)->toBeTrue();
    expect($expense->transaction_date->format('Y-m-d'))->toBe('2024-10-10');
    expect($expense->effective_date->format('Y-m-d'))->toBe('2024-09-30');
});

test('users can delete existing expenses', function () {
    $user = login();

    $expense = Expense::factory()->create(['user_id' => $user->id]);

    $this->assertModelExists($expense);

    $this->delete(route('expenses.delete', $expense), ['password' => 'password']);

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

    $payload = $expenseRestricted->toArray();

    // create user owned category to pass validation. Gate auth check happens right after validation.
    $payload['category_id'] = Category::factory()->create(['user_id' => $user->id])->id;

    $this->patch(route('expenses.update', $expenseRestricted), $payload)
        ->assertForbidden();
});

it('will return a 403 if user attempts to delete expenses from other accounts', function () {
    $user = login();

    $expenseRestricted = Expense::factory()->create();

    $payload = $expenseRestricted->toArray();

    // create user owned category to pass validation. authentication happens right after validation
    $payload['category_id'] = Category::factory()->create(['user_id' => $user->id])->id;

    $this->delete(route('expenses.delete', $expenseRestricted), $payload)
        ->assertForbidden();
});
