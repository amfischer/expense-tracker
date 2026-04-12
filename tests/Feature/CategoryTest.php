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
        ->assertSessionHasErrors(['message' => 'Category is linked to ' . count($expenses) . ' expenses. Remove these relationships before deleting.']);
});

test('default category cannot be deleted', function () {
    $category = Category::where(['user_id' => $this->user->id, 'name' => Category::DEFAULT_NAME])->first();

    $this->delete(route('categories.delete', $category))
        ->assertRedirect()
        ->assertSessionHasErrors(['message' => 'Default category cannot be deleted.']);
});

/**
 * PARENT/CHILD CATEGORY TESTS
 */
test('users can create a subcategory under a parent', function () {
    $parent = Category::factory()->create(['user_id' => $this->user->id]);

    $payload = [
        'name'      => 'Child Category',
        'color'     => '#ff0000',
        'parent_id' => $parent->id,
    ];

    $this->post(route('categories.store'), $payload);

    $this->assertDatabaseHas('categories', [
        'name'      => 'Child Category',
        'parent_id' => $parent->id,
    ]);
});

test('users cannot create a subcategory under another subcategory', function () {
    $parent = Category::factory()->create(['user_id' => $this->user->id]);
    $child = Category::factory()->child($parent)->create();

    $payload = [
        'name'      => 'Grandchild Category',
        'color'     => '#ff0000',
        'parent_id' => $child->id,
    ];

    $this->post(route('categories.store'), $payload)
        ->assertSessionHasErrors('parent_id');
});

test('users cannot assign a parent category from another user', function () {
    $otherParent = Category::factory()->create();

    $payload = [
        'name'      => 'Sneaky Category',
        'color'     => '#ff0000',
        'parent_id' => $otherParent->id,
    ];

    $this->post(route('categories.store'), $payload)
        ->assertSessionHasErrors('parent_id');
});

it('will block deletion of a parent category that has children', function () {
    $parent = Category::factory()->create(['user_id' => $this->user->id]);
    Category::factory()->child($parent)->create();

    $this->delete(route('categories.delete', $parent))
        ->assertRedirect()
        ->assertSessionHasErrors(['message' => 'Category has 1 subcategories. Remove them before deleting.']);
});

test('users can delete a child category', function () {
    $parent = Category::factory()->create(['user_id' => $this->user->id]);
    $child = Category::factory()->child($parent)->create();

    $this->delete(route('categories.delete', $child));

    $this->assertModelMissing($child);
});

test('users cannot set a category as its own parent', function () {
    $category = Category::factory()->create(['user_id' => $this->user->id]);

    $payload = [
        'name'      => $category->name,
        'color'     => $category->color,
        'parent_id' => $category->id,
    ];

    $this->put(route('categories.update', $category), $payload)
        ->assertSessionHasErrors('parent_id');
});

test('a parent category with children cannot become a subcategory', function () {
    $parent = Category::factory()->create(['user_id' => $this->user->id]);
    Category::factory()->child($parent)->create();
    $otherParent = Category::factory()->create(['user_id' => $this->user->id]);

    $payload = [
        'name'      => $parent->name,
        'color'     => $parent->color,
        'parent_id' => $otherParent->id,
    ];

    $this->put(route('categories.update', $parent), $payload)
        ->assertSessionHasErrors('parent_id');
});

test('users can update a category to add a parent', function () {
    $parent = Category::factory()->create(['user_id' => $this->user->id]);
    $category = Category::factory()->create(['user_id' => $this->user->id]);

    $payload = [
        'name'      => $category->name,
        'color'     => $category->color,
        'parent_id' => $parent->id,
    ];

    $this->put(route('categories.update', $category), $payload);

    expect($category->refresh()->parent_id)->toBe($parent->id);
});

test('users can remove a parent from a category', function () {
    $parent = Category::factory()->create(['user_id' => $this->user->id]);
    $child = Category::factory()->child($parent)->create();

    $payload = [
        'name'      => $child->name,
        'color'     => $child->color,
        'parent_id' => null,
    ];

    $this->put(route('categories.update', $child), $payload);

    expect($child->refresh()->parent_id)->toBeNull();
});

test('the index page groups categories by parent', function () {
    $parent = Category::factory()->create(['user_id' => $this->user->id]);
    $child = Category::factory()->child($parent)->create();
    $standalone = Category::factory()->create(['user_id' => $this->user->id]);

    $this->get(route('categories.index'))
        ->assertOk()
        ->assertSee($parent->name)
        ->assertSee($child->name)
        ->assertSee($standalone->name);
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
