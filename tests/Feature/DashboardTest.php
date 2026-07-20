<?php

use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;

use function Pest\Laravel\get;
use function Pest\Laravel\getJson;

it('can view the dashboard', function () {
    login();

    get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Dashboard/Index'));
});

it('can fetch summary details for a date range', function () {
    $user = login();

    Expense::factory()->for($user)->create([
        'effective_date' => '2026-01-15',
        'amount'         => '100.00',
    ]);

    Income::factory()->for($user)->create([
        'effective_date' => '2026-01-10',
        'amount'         => '500.00',
    ]);

    getJson(route('dashboard.summary.details', [
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

    Expense::factory()->for($user)->count(3)->create([
        'effective_date' => '2026-01-15',
    ]);

    $response = getJson(route('dashboard.summary.details', [
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

it('rolls child category expenses up under their parent', function () {
    $user = login();

    $parent = Category::factory()->for($user)->create(['name' => 'Food', 'color' => '#ff0000']);
    $groceries = Category::factory()->child($parent)->create(['name' => 'Groceries']);
    $dining = Category::factory()->child($parent)->create(['name' => 'Dining']);

    Expense::factory()->for($user)->create([
        'category_id'    => $groceries->id,
        'amount'         => 10,
        'effective_date' => '2026-01-15',
    ]);

    Expense::factory()->for($user)->create([
        'category_id'    => $dining->id,
        'amount'         => 20,
        'effective_date' => '2026-01-16',
    ]);

    $response = getJson(route('dashboard.summary.details', [
        'date_from' => '2026-01-01',
        'date_to'   => '2026-01-31',
    ]));

    $data = $response->assertOk()->json();

    // Both child expenses collapse into a single row labeled with the parent's name/color.
    expect($data['expense_categories'])->toHaveCount(1);
    expect($data['expense_categories'][0])
        ->name->toBe('Food')
        ->color->toBe('#ff0000')
        ->count->toBe(2)
        ->total_raw->toBe(3000);

    // The most frequent category stat rolls child counts up under the parent too.
    expect($data['stats']['most_frequent_category'])->toBe('Food');
    expect($data['stats']['most_frequent_category_count'])->toBe(2);
});

it('returns income sources grouped by source', function () {
    $user = login();

    Income::factory()->for($user)->create([
        'effective_date' => '2026-01-10',
        'source'         => 'Salary',
    ]);

    Income::factory()->for($user)->create([
        'effective_date' => '2026-01-15',
        'source'         => 'Freelance',
    ]);

    $response = getJson(route('dashboard.summary.details', [
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
    Expense::factory()->for($user)->create([
        'effective_date' => '2026-01-15',
        'amount'         => '200.00',
    ]);

    Income::factory()->for($user)->create([
        'effective_date' => '2026-01-10',
        'amount'         => '1000.00',
    ]);

    $response = getJson(route('dashboard.summary.details', [
        'date_from' => '2026-01-01',
        'date_to'   => '2026-01-31',
    ]));

    $data = $response->json();

    // (1000 - 200) / 1000 * 100 = 80%
    expect($data['stats']['savings_rate'])->toEqual(80);
});

it('returns null savings rate when there is no income', function () {
    $user = login();

    Expense::factory()->for($user)->create([
        'effective_date' => '2026-01-15',
    ]);

    $response = getJson(route('dashboard.summary.details', [
        'date_from' => '2026-01-01',
        'date_to'   => '2026-01-31',
    ]));

    $data = $response->json();

    expect($data['stats']['savings_rate'])->toBeNull();
});

it('validates date parameters', function () {
    login();

    getJson(route('dashboard.summary.details'))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['date_from', 'date_to']);
});
