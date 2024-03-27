<?php

use App\Models\Expense;
use App\Models\Tag;

test('amount & fees return a formatted string with two decimal places', function () {
    $amount = 10.2;
    $fees = 1.79;
    $expense = Expense::factory()->create(['amount' => $amount, 'foreign_currency_conversion_fee' => $fees, 'currency' => 'USD']);

    expect($expense->amount)
        ->toBeString()
        ->toBe('10.20');

    expect($expense->foreign_currency_conversion_fee)
        ->toBeString()
        ->toBe('1.79');
});

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

test('tagIds returns an array of ids representing tags attached to the expense', function () {
    $tags = Tag::factory(3)->create();

    $tagIds = [];
    foreach ($tags as $tag) {
        $tagIds[] = $tag->id;
    }

    $expense = Expense::factory()->create();

    $expense->tags()->attach($tagIds);

    expect($expense->tagIds)
        ->toBeArray()
        ->toEqual($tagIds);
});
