<?php

use App\Models\Category;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    login();

    $this->user = Auth::user();
});

it('can show categories', function () {
    $category = Category::factory()->create(['user_id' => $this->user->id]);

    $this->get(route('categories.index'))
        ->assertOk()
        ->assertSee($category->name);
});

it("will not show another user's categories", function () {
    $categoryRestricted = Category::factory()->create();

    $this->get(route('categories.index'))
        ->assertOk()
        ->assertDontSee($categoryRestricted->name);

    $this->assertDatabaseMissing('categories', [
        'user_id' => $this->user->id,
        'id'      => $categoryRestricted->id,
    ]);
});

test('users can create new categories', function () {

    $payload = Category::factory()->make(['user_id' => $this->user->id])->toArray();

    $this->post(route('categories.store'), $payload);

    $this->assertDatabaseHas('categories', $payload);
});

test('users can update existing categories', function () {

    $category = Category::factory()->create(['user_id' => $this->user->id]);

    $payload = [
        'name'  => 'new name',
        'color' => '#ffee00',
    ];

    $this->put(route('categories.update', $category), $payload);

    $this->assertDatabaseHas('categories', $payload);

    expect($category->refresh()->name)->toBe('new name');
    expect($category->refresh()->color)->toBe('#ffee00');
});

test('users cannot rename the default category', function () {
    $category = Category::where(['user_id' => $this->user->id, 'name' => Category::DEFAULT_NAME])->first();

    $payload = [
        'name'  => 'updated name',
        'color' => '#ffee22',
    ];

    $this->put(route('categories.update', $category), $payload)
        ->assertRedirect()
        ->assertSessionHasErrors(['name' => 'The default category cannot be renamed.']);
});

test('users can delete existing categories', function () {
    $category = Category::factory()->create(['user_id' => $this->user->id]);

    $this->assertModelExists($category);

    $this->delete(route('categories.delete', $category));

    $this->assertModelMissing($category);
    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

it('will block category deletion if the category is linked to any expenses', function () {
    $category = Category::factory()->create(['user_id' => $this->user->id]);
    $expenses = Expense::factory(2)->create(['user_id' => $this->user->id, 'category_id' => $category->id]);

    $this->delete(route('categories.delete', $category))
        ->assertRedirect()
        ->assertSessionHasErrors(['message' => 'category is linked to ' . count($expenses) . ' expenses. Remove these relationships before deleting.']);
});

test('default category cannot be deleted', function () {
    $category = Category::where(['user_id' => $this->user->id, 'name' => Category::DEFAULT_NAME])->first();

    $this->delete(route('categories.delete', $category))
        ->assertRedirect()
        ->assertSessionHasErrors(['message' => 'Default category cannot be deleted.']);
});

/**
 * AUTHORIZATION TESTS
 */
it('will return a 403 if user attempts to update categories from other accounts', function () {
    $categoryRestricted = Category::factory()->create();

    $this->put(route('categories.update', $categoryRestricted), [])
        ->assertForbidden();
});

it('will return a 403 if user attempts to delete categories from other accounts', function () {
    $categoryRestricted = Category::factory()->create();

    $this->delete(route('categories.delete', $categoryRestricted))
        ->assertForbidden();
});
