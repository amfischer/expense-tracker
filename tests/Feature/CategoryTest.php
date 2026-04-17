<?php

use App\Models\Category;
use App\Models\Expense;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\assertModelMissing;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

beforeEach(fn () => $this->user = login());

describe('index', function () {
    it("shows the user's categories", function () {
        $category = Category::factory()->for($this->user)->create();

        get(route('categories.index'))
            ->assertOk()
            ->assertSee($category->name);
    });

    it("does not show another user's categories", function () {
        $otherCategory = Category::factory()->create();

        get(route('categories.index'))
            ->assertOk()
            ->assertDontSee($otherCategory->name);

        assertDatabaseMissing('categories', [
            'user_id' => $this->user->id,
            'id'      => $otherCategory->id,
        ]);
    });

    it('nests children under their parent category', function () {
        $parent = Category::factory()->for($this->user)->create(['name' => 'Parent']);
        $child = Category::factory()->child($parent)->create(['name' => 'Child']);
        Category::factory()->for($this->user)->create(['name' => 'Standalone']);

        $response = get(route('categories.index'))->assertOk();

        $categories = collect($response->original->getData()['page']['props']['categories']);

        // Child should not appear at the top level
        expect($categories)->toHaveCount(3) // default + parent + standalone
            ->and($categories->pluck('name'))->not->toContain('Child');

        // Child should be nested under parent
        $parentCategory = $categories->firstWhere('name', 'Parent');
        expect($parentCategory['children'])->toHaveCount(1)
            ->and($parentCategory['children'][0]['name'])->toBe('Child');
    });
});

describe('store', function () {
    it('creates a new category', function () {
        $payload = [
            'name'  => 'Groceries',
            'color' => '#00ff00',
        ];

        post(route('categories.store'), $payload)
            ->assertRedirect();

        assertDatabaseHas('categories', [
            ...$payload,
            'user_id' => $this->user->id,
        ]);
    });

    it('creates a subcategory under a parent', function () {
        $parent = Category::factory()->for($this->user)->create();

        $payload = [
            'name'      => 'Child Category',
            'color'     => '#ff0000',
            'parent_id' => $parent->id,
        ];

        post(route('categories.store'), $payload)
            ->assertRedirect();

        assertDatabaseHas('categories', [
            'name'      => 'Child Category',
            'parent_id' => $parent->id,
        ]);
    });

    it('rejects nesting more than one level deep', function () {
        $parent = Category::factory()->for($this->user)->create();
        $child = Category::factory()->child($parent)->create();

        post(route('categories.store'), [
            'name'      => 'Grandchild Category',
            'color'     => '#ff0000',
            'parent_id' => $child->id,
        ])->assertSessionHasErrors('parent_id');
    });

    it('rejects a parent category from another user', function () {
        $otherParent = Category::factory()->create();

        post(route('categories.store'), [
            'name'      => 'Sneaky Category',
            'color'     => '#ff0000',
            'parent_id' => $otherParent->id,
        ])->assertSessionHasErrors('parent_id');
    });
});

describe('update', function () {
    it('updates an existing category', function () {
        $category = Category::factory()->for($this->user)->create();

        $payload = [
            'name'  => 'new name',
            'color' => '#ffee00',
        ];

        put(route('categories.update', $category), $payload)
            ->assertRedirect();

        $category->refresh();

        expect($category->name)->toBe('new name')
            ->and($category->color)->toBe('#ffee00');
    });

    it('rejects renaming the default category', function () {
        $category = Category::where(['user_id' => $this->user->id, 'name' => Category::DEFAULT_NAME])->first();

        put(route('categories.update', $category), [
            'name'  => 'updated name',
            'color' => '#ffee22',
        ])
            ->assertRedirect()
            ->assertSessionHasErrors(['name' => 'The default category cannot be renamed.']);
    });

    it('rejects setting a category as its own parent', function () {
        $category = Category::factory()->for($this->user)->create();

        put(route('categories.update', $category), [
            'name'      => $category->name,
            'color'     => $category->color,
            'parent_id' => $category->id,
        ])->assertSessionHasErrors('parent_id');
    });

    it('rejects nesting a parent that already has children', function () {
        $parent = Category::factory()->for($this->user)->create();
        Category::factory()->child($parent)->create();
        $otherParent = Category::factory()->for($this->user)->create();

        put(route('categories.update', $parent), [
            'name'      => $parent->name,
            'color'     => $parent->color,
            'parent_id' => $otherParent->id,
        ])->assertSessionHasErrors('parent_id');
    });

    it('can add a parent to a category', function () {
        $parent = Category::factory()->for($this->user)->create();
        $category = Category::factory()->for($this->user)->create();

        put(route('categories.update', $category), [
            'name'      => $category->name,
            'color'     => $category->color,
            'parent_id' => $parent->id,
        ])->assertRedirect();

        expect($category->refresh()->parent_id)->toBe($parent->id);
    });

    it('can remove a parent from a category', function () {
        $parent = Category::factory()->for($this->user)->create();
        $child = Category::factory()->child($parent)->create();

        put(route('categories.update', $child), [
            'name'      => $child->name,
            'color'     => $child->color,
            'parent_id' => null,
        ])->assertRedirect();

        expect($child->refresh()->parent_id)->toBeNull();
    });
});

describe('delete', function () {
    it('deletes an existing category', function () {
        $category = Category::factory()->for($this->user)->create();

        delete(route('categories.delete', $category))
            ->assertRedirect();

        assertModelMissing($category);
    });

    it('blocks deletion when linked to expenses', function () {
        $category = Category::factory()->for($this->user)->create();
        $expenses = Expense::factory(2)->for($this->user)->for($category)->create();

        delete(route('categories.delete', $category))
            ->assertRedirect()
            ->assertSessionHasErrors(['message' => 'Category is linked to ' . count($expenses) . ' expenses. Remove these relationships before deleting.']);
    });

    it('blocks deletion of the default category', function () {
        $category = Category::where(['user_id' => $this->user->id, 'name' => Category::DEFAULT_NAME])->first();

        delete(route('categories.delete', $category))
            ->assertRedirect()
            ->assertSessionHasErrors(['message' => 'Default category cannot be deleted.']);
    });

    it('blocks deletion of a parent with children', function () {
        $parent = Category::factory()->for($this->user)->create();
        Category::factory()->child($parent)->create();

        delete(route('categories.delete', $parent))
            ->assertRedirect()
            ->assertSessionHasErrors(['message' => 'Category has 1 subcategories. Remove them before deleting.']);
    });

    it('can delete a child category', function () {
        $parent = Category::factory()->for($this->user)->create();
        $child = Category::factory()->child($parent)->create();

        delete(route('categories.delete', $child))
            ->assertRedirect();

        assertModelMissing($child);
    });
});

describe('authorization', function () {
    it("returns 403 when updating another user's category", function () {
        $otherCategory = Category::factory()->create();

        put(route('categories.update', $otherCategory), [])
            ->assertForbidden();
    });

    it("returns 403 when deleting another user's category", function () {
        $otherCategory = Category::factory()->create();

        delete(route('categories.delete', $otherCategory))
            ->assertForbidden();
    });
});
