# Dashboard Summary Dialog Implementation Plan

## Overview
Add a detailed summary dialog that opens when clicking "View" on a TotalsSummaryCard, showing category breakdowns, income sources, and quick stats.

## Backend Changes

### 1. Extend `ExpenseService` with new method
**File:** `app/Services/ExpenseService.php`

Add `getDetailedSummary(User $user, string $from, string $to): array` that returns:

```php
[
    'totals' => [
        'expenses' => string,      // formatted total
        'income' => string,        // formatted total
        'difference' => string,    // formatted difference
        'is_loss' => bool,
    ],
    'expense_categories' => [
        [
            'name' => string,
            'color' => string,
            'total' => string,     // formatted
            'total_raw' => int,    // cents for sorting
            'percentage' => float, // e.g., 25.5
            'count' => int,
        ],
        // ... ordered by total_raw DESC
    ],
    'income_sources' => [
        [
            'source' => string,
            'total' => string,
            'total_raw' => int,
            'percentage' => float,
            'count' => int,
        ],
        // ... ordered by total_raw DESC
    ],
    'stats' => [
        'avg_daily_spent' => string,       // formatted
        'avg_daily_earned' => string,      // formatted
        'most_frequent_category' => string, // category name
        'savings_rate' => float,           // percentage: ((income - expenses) / income) * 100
                                           // positive = saved, negative = overspent, 0 = break-even
                                           // null if no income (avoid division by zero)
    ],
]
```

### 2. Update DashboardController
**File:** `app/Http/Controllers/DashboardController.php`

Modify `getSummaryDetails()` to call the new `ExpenseService::getDetailedSummary()` method instead of `User::getExpenseSummaryDetails()`.

---

## Frontend Changes

### 1. Update `TotalsSummaryCard.vue`
**File:** `resources/js/Pages/Dashboard/Partials/TotalsSummaryCard.vue`

- Add `defineEmits(['view'])`
- Wire View button to emit the event: `@click="$emit('view', report)"`

### 2. Redesign `SummaryDialog.vue`
**File:** `resources/js/Pages/Dashboard/Partials/SummaryDialog.vue`

Replace filter-specific content with summary display:
- Remove the `scout` prop and all references to it (clear filters button)
- Remove the `<slot />` tag - content will be rendered directly in the component

**Props:**
- `open` (Boolean) - dialog visibility
- `data` (Object) - the detailed summary data from API
- `loading` (Boolean) - show loading state while fetching

**Sections to display:**
1. **Header** - Period label (e.g., "January 2026") with totals
2. **Expense Breakdown** - List of categories with color dots, totals, percentages, progress bars
3. **Income Breakdown** - List of sources with totals and percentages
4. **Quick Stats** - Grid showing avg daily spent/earned, most frequent category, savings rate (can be positive, negative, or zero)

### 3. Update `Index.vue`
**File:** `resources/js/Pages/Dashboard/Index.vue`

Add state and handlers:
```js
const summaryDialogOpen = ref(false);
const summaryDialogData = ref(null);
const summaryDialogLoading = ref(false);
const summaryDialogLabel = ref('');

const openSummaryDialog = (report) => {
    summaryDialogOpen.value = true;
    summaryDialogLoading.value = true;
    summaryDialogLabel.value = report.label;

    axios.get(route('dashboard.summary.details'), {
        params: { date_from: report.date_from, date_to: report.date_to }
    }).then(resp => {
        summaryDialogData.value = resp.data;
        summaryDialogLoading.value = false;
    });
};
```

Add to template:
- Import and render `SummaryDialog` once at page level
- Pass `@view="openSummaryDialog"` to each `TotalsSummaryCard`

---

## Files to Modify

| File | Changes |
|------|---------|
| `app/Services/ExpenseService.php` | Add `getDetailedSummary()` method |
| `app/Http/Controllers/DashboardController.php` | Update `getSummaryDetails()` |
| `resources/js/Pages/Dashboard/Partials/TotalsSummaryCard.vue` | Add emit for view button |
| `resources/js/Pages/Dashboard/Partials/SummaryDialog.vue` | Complete redesign for summary display |
| `resources/js/Pages/Dashboard/Index.vue` | Add dialog state, import, and handler |

---

## Verification

1. Run backend tests: `docker-compose exec php-fpm sh -c "./vendor/bin/pest tests/Feature/DashboardTest.php"`
2. Navigate to dashboard and click "View" on any card
3. Verify dialog opens with correct data for the selected period
4. Verify category percentages sum to ~100%
5. Verify income sources are grouped correctly
6. Verify quick stats calculate correctly
