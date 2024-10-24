<?php

use App\Http\Requests\ExpenseRequest;
use App\Models\Category;
use App\Models\Expense;
use Illuminate\Support\Facades\Validator;

// VALIDATION TESTS

it('will validate the selected category belongs to the authenticated user', function () {
    $user = login();

    $payload = Expense::factory()->make(['user_id' => $user->id])->toArray();

    $request = new ExpenseRequest();

    /**
     * using valid category
     */
    $validator = Validator::make($payload, $request->rules(), $request->messages());

    expect($validator->passes())->toBeTrue();
    expect($validator->messages()->toArray())->toBeEmpty();

    /**
     * using invalid category
     */
    $payload['category_id'] = Category::factory()->create()->id;

    $validator = Validator::make($payload, $request->rules(), $request->messages());

    expect($validator->fails())->toBeTrue();
    expect($validator->messages()->toArray())->toMatchArray([
        'category_id' => ['Invalid category selection.'],
    ]);

});
