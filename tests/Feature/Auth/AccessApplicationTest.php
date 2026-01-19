<?php

use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

// Direct Gate tests
test('user with email in allowed list can access application', function () {
    $user = User::factory()->create(['email' => 'allowed@example.com']);

    config(['custom.application_access' => 'allowed@example.com']);

    $this->actingAs($user);

    expect(Gate::allows('access-application'))->toBeTrue();
});

test('user with email not in allowed list cannot access application', function () {
    $user = User::factory()->create(['email' => 'denied@example.com']);

    config(['custom.application_access' => 'allowed@example.com']);

    $this->actingAs($user);

    expect(Gate::denies('access-application'))->toBeTrue();
});

test('gate allows access when user email is in comma-separated list', function () {
    $user = User::factory()->create(['email' => 'second@example.com']);

    config(['custom.application_access' => 'first@example.com,second@example.com,third@example.com']);

    $this->actingAs($user);

    expect(Gate::allows('access-application'))->toBeTrue();
});

test('gate denies access when config is empty', function () {
    $user = User::factory()->create();

    config(['custom.application_access' => '']);

    $this->actingAs($user);

    expect(Gate::denies('access-application'))->toBeTrue();
});

// Route middleware tests
describe('Dashboard Routes', function () {
    it('prohibits users without explicit access', function () {
        $user = User::factory()->create(['email' => 'denied@example.com']);

        config(['custom.application_access' => 'allowed@example.com']);

        $this->actingAs($user);
        $this->get(route('dashboard'))->assertForbidden();
        $this->get(route('dashboard.summary.details'))->assertForbidden();
    });

    it('allows users with explicit access', function () {
        $user = User::factory()->create(['email' => 'allowed@example.com']);

        config(['custom.application_access' => 'allowed@example.com']);

        $this->actingAs($user);
        $this->get(route('dashboard'))->assertOk();
        $this->get(route('dashboard.summary.details'))->assertRedirect();
    });
});

describe('Incomes Routes', function () {
    it('prohibits users without explicit access', function () {
        $user = User::factory()->create(['email' => 'denied@example.com']);

        config(['custom.application_access' => 'allowed@example.com']);

        $income = Income::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->get(route('incomes.index'))->assertForbidden();
        $this->post(route('incomes.store'))->assertForbidden();
        $this->get(route('incomes.edit', $income))->assertForbidden();
        $this->patch(route('incomes.update', $income))->assertForbidden();
        $this->delete(route('incomes.delete', $income))->assertForbidden();
    });

    it('allows users with explicit access', function () {
        $user = User::factory()->create(['email' => 'allowed@example.com']);

        config(['custom.application_access' => 'allowed@example.com']);

        $income = Income::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->get(route('incomes.index'))->assertOk();
        $this->post(route('incomes.store'))->assertRedirect();
        $this->get(route('incomes.edit', $income))->assertOk();
        $this->patch(route('incomes.update', $income))->assertRedirect();
        $this->delete(route('incomes.delete', $income))->assertRedirect();
    });
});

describe('Expense Routes', function () {
    it('prohibits users without explicit access', function () {
        $user = User::factory()->create(['email' => 'denied@example.com']);

        config(['custom.application_access' => 'allowed@example.com']);

        $expense = Expense::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->get(route('expenses.index'))->assertForbidden();
        $this->post(route('expenses.store'))->assertForbidden();
        $this->get(route('expenses.edit', $expense))->assertForbidden();
        $this->patch(route('expenses.update', $expense))->assertForbidden();
        $this->delete(route('expenses.delete', $expense))->assertForbidden();
    });

    it('allows users with explicit access', function () {
        $user = User::factory()->create(['email' => 'allowed@example.com']);

        config(['custom.application_access' => 'allowed@example.com']);

        $expense = Expense::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->get(route('expenses.index'))->assertOk();
        $this->post(route('expenses.store'))->assertRedirect();
        $this->get(route('expenses.edit', $expense))->assertOk();
        $this->patch(route('expenses.update', $expense))->assertRedirect();
        $this->delete(route('expenses.delete', $expense))->assertRedirect();
    });
});

describe('Category Routes', function () {
    it('prohibits users without explicit access', function () {
        $user = User::factory()->create(['email' => 'denied@example.com']);

        config(['custom.application_access' => 'allowed@example.com']);

        $category = Category::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->get(route('categories.index'))->assertForbidden();
        $this->post(route('categories.store'))->assertForbidden();
        $this->put(route('categories.update', $category))->assertForbidden();
        $this->delete(route('categories.delete', $category))->assertForbidden();
    });

    it('allows users with explicit access', function () {
        $user = User::factory()->create(['email' => 'allowed@example.com']);

        config(['custom.application_access' => 'allowed@example.com']);

        $category = Category::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->get(route('categories.index'))->assertOk();
        $this->post(route('categories.store'))->assertRedirect();
        $this->put(route('categories.update', $category))->assertRedirect();
        $this->delete(route('categories.delete', $category))->assertRedirect();
    });
});

describe('Profile Routes', function () {
    it('prohibits users without explicit access', function () {
        $user = User::factory()->create(['email' => 'denied@example.com']);

        config(['custom.application_access' => 'allowed@example.com']);

        $this->actingAs($user);

        $this->get(route('profile.edit'))->assertForbidden();
        $this->patch(route('profile.update', $user))->assertForbidden();
        $this->delete(route('profile.destroy', $user))->assertForbidden();
    });

    it('allows users with explicit access', function () {
        $user = User::factory()->create(['email' => 'allowed@example.com']);

        config(['custom.application_access' => 'allowed@example.com']);

        $this->actingAs($user);

        $this->get(route('profile.edit'))->assertOk();
        $this->patch(route('profile.update', $user))->assertRedirect();
        $this->delete(route('profile.destroy', $user))->assertRedirect();
    });
});
