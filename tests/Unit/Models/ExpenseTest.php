<?php

use App\Models\Expense;

test('amount & fees store values as an integer representing number of cents', function () {
    $amount = 62.33;
    $fees = 2;
    Expense::factory()->create(['amount' => $amount, 'foreign_currency_conversion_fee' => $fees, 'currency' => 'USD']);

    $amountInCents = $amount * 100;
    $feesInCents = $fees * 100;

    $this->assertDatabaseHas('expenses', ['amount' => $amountInCents, 'foreign_currency_conversion_fee' => $feesInCents]);
});

test('amountPretty & feesPretty return a formatted string with a dollar-sign and correct comma and decimal placement', function () {
    $amount = 1233.89;
    $fees = 29.99;
    $expense = Expense::factory()->create(['amount' => $amount, 'foreign_currency_conversion_fee' => $fees, 'currency' => 'USD']);

    expect($expense->amountPretty)
        ->toBeString()
        ->toBe('$1,233.89');

    expect($expense->foreign_currency_conversion_fee_pretty)
        ->toBeString()
        ->toBe('$29.99');
});

test('total returns the sum of amount & fees as a formatted string', function () {
    $amount = 955.22;
    $fees = 123.45;
    $expense = Expense::factory()->create(['amount' => $amount, 'foreign_currency_conversion_fee' => $fees, 'currency' => 'USD']);

    expect($expense->total)
        ->toBeString()
        ->toBe('$1,078.67');
});
