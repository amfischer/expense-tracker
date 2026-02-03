<?php

use App\Models\Expense;
use App\Models\Income;

it('can view the dashboard', function () {
    login();

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Dashboard/Index'));
});

it('can fetch summary details for a date range', function () {
    $user = login();

    $expense = Expense::factory()->create([
        'user_id'        => $user->id,
        'effective_date' => '2026-01-15',
        'amount'         => '100.00',
    ]);

    $income = Income::factory()->create([
        'user_id'        => $user->id,
        'effective_date' => '2026-01-10',
        'amount'         => '500.00',
    ]);

    $this->getJson(route('dashboard.summary.details', [
        'date_from' => '2026-01-01',
        'date_to'   => '2026-01-31',
    ]))
        ->assertOk()
        ->assertJsonStructure([
            'totals' => ['expenses', 'income', 'difference', 'is_loss'],
            'expense_categories',
            'income_sources',
            'stats'  => ['avg_daily_spent', 'avg_daily_earned', 'most_frequent_category', 'savings_rate'],
        ]);
});

it('returns expense categories grouped by category', function () {
    $user = login();

    Expense::factory()->count(3)->create([
        'user_id'        => $user->id,
        'effective_date' => '2026-01-15',
    ]);

    $response = $this->getJson(route('dashboard.summary.details', [
        'date_from' => '2026-01-01',
        'date_to'   => '2026-01-31',
    ]));

    $response->assertOk();

    $data = $response->json();

    expect($data['expense_categories'])->toBeArray();

    foreach ($data['expense_categories'] as $category) {
        expect($category)->toHaveKeys(['name', 'color', 'total', 'total_raw', 'percentage', 'count']);
    }
});

it('returns income sources grouped by source', function () {
    $user = login();

    Income::factory()->create([
        'user_id'        => $user->id,
        'effective_date' => '2026-01-10',
        'source'         => 'Salary',
    ]);

    Income::factory()->create([
        'user_id'        => $user->id,
        'effective_date' => '2026-01-15',
        'source'         => 'Freelance',
    ]);

    $response = $this->getJson(route('dashboard.summary.details', [
        'date_from' => '2026-01-01',
        'date_to'   => '2026-01-31',
    ]));

    $response->assertOk();

    $data = $response->json();

    expect($data['income_sources'])->toHaveCount(2);

    foreach ($data['income_sources'] as $source) {
        expect($source)->toHaveKeys(['source', 'total', 'total_raw', 'percentage', 'count']);
    }
});

it('calculates savings rate correctly', function () {
    $user = login();

    // $200 expenses, $1000 income = 80% savings rate
    Expense::factory()->create([
        'user_id'        => $user->id,
        'effective_date' => '2026-01-15',
        'amount'         => '200.00',
    ]);

    Income::factory()->create([
        'user_id'        => $user->id,
        'effective_date' => '2026-01-10',
        'amount'         => '1000.00',
    ]);

    $response = $this->getJson(route('dashboard.summary.details', [
        'date_from' => '2026-01-01',
        'date_to'   => '2026-01-31',
    ]));

    $data = $response->json();

    // (1000 - 200) / 1000 * 100 = 80%
    expect($data['stats']['savings_rate'])->toEqual(80);
});

it('returns null savings rate when there is no income', function () {
    $user = login();

    Expense::factory()->create([
        'user_id'        => $user->id,
        'effective_date' => '2026-01-15',
    ]);

    $response = $this->getJson(route('dashboard.summary.details', [
        'date_from' => '2026-01-01',
        'date_to'   => '2026-01-31',
    ]));

    $data = $response->json();

    expect($data['stats']['savings_rate'])->toBeNull();
});

it('validates date parameters', function () {
    login();

    $this->getJson(route('dashboard.summary.details'))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['date_from', 'date_to']);
});
