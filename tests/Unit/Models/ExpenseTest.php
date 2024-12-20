<?php

use App\Models\Expense;

test('amount stores value as an integer representing number of cents', function () {
    $amount = 62.33;
    Expense::factory()->create(['amount' => $amount, 'currency' => 'USD']);

    $amountInCents = $amount * 100;

    $this->assertDatabaseHas('expenses', ['amount' => $amountInCents]);
});

test('amountPretty returns a formatted string with a dollar-sign and correct comma and decimal placement', function () {
    $amount = 1233.89;
    $expense = Expense::factory()->create(['amount' => $amount, 'currency' => 'USD']);

    expect($expense->amount_pretty)
        ->toBeString()
        ->toBe('$1,233.89');

});
