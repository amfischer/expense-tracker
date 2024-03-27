<?php

use App\Http\Requests\ExpenseRequest;
use App\Models\Category;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

beforeEach(function () {
    login();

    $this->user = Auth::user();

    // authenticated user expense data (but has invalid category)
    $this->requestData = Expense::factory()->make(['user_id' => $this->user->id])->toArray();
});

// VALIDATION TESTS

it('will validate the selected category belongs to the authenticated user', function () {

    $request = new ExpenseRequest();

    /**
     * using valid category
     */
    $validator = Validator::make($this->requestData, $request->rules(), $request->messages());

    expect($validator->passes())->toBeTrue();
    expect($validator->messages()->toArray())->toBeEmpty();

    /**
     * using invalid category
     */
    $this->requestData['category_id'] = Category::factory()->create()->id;

    $validator = Validator::make($this->requestData, $request->rules(), $request->messages());

    expect($validator->fails())->toBeTrue();
    expect($validator->messages()->toArray())->toMatchArray([
        'category_id' => ['Invalid category selection.'],
    ]);

});
