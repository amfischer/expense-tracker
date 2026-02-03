<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;

class ExpenseService
{
    public function getDetailedSummary(User $user, string $from, string $to): array
    {
        $dateRange = [$from, $to];
        $formatter = app(IntlMoneyFormatter::class);

        // Fetch expenses and incomes
        $expenses = $user->expenses()->with('category')->whereBetween('effective_date', $dateRange)->get();
        $incomes = $user->incomes()->whereBetween('effective_date', $dateRange)->get();

        // Calculate totals

        /** @var Money $expensesTotal */
        $expensesTotal = $expenses->reduce(
            fn (Money $carry, Expense $item) => $carry->add(Money::USD($item->amount)),
            Money::USD(0)
        );

        /** @var Money $incomesTotal */
        $incomesTotal = $incomes->reduce(
            fn (Money $carry, Income $item) => $carry->add(Money::USD($item->amount)),
            Money::USD(0)
        );
        $difference = $incomesTotal->subtract($expensesTotal);
        $isLoss = $incomesTotal->lessThan($expensesTotal);

        // Group expenses by category
        $expenseCategories = $this->buildExpenseCategories($expenses, $expensesTotal, $formatter);

        // Group incomes by source
        $incomeSources = $this->buildIncomeSources($incomes, $incomesTotal, $formatter);

        // Calculate stats
        $stats = $this->buildStats($expenses, $incomes, $expensesTotal, $incomesTotal, $from, $to, $formatter);

        return [
            'totals'             => [
                'expenses'          => $formatter->format($expensesTotal),
                'income'            => $formatter->format($incomesTotal),
                'difference'        => $formatter->format($difference),
                'is_loss'           => $isLoss,
                'transaction_count' => $expenses->count(),
            ],
            'expense_categories' => $expenseCategories,
            'income_sources'     => $incomeSources,
            'stats'              => $stats,
        ];
    }

    /**
     * @param  Collection<int, Expense>  $expenses
     */
    protected function buildExpenseCategories(Collection $expenses, Money $total, IntlMoneyFormatter $formatter): array
    {
        $categories = [];
        $totalAmount = (int) $total->getAmount();

        foreach ($expenses as $expense) {
            $categoryId = $expense->category_id;

            if (! isset($categories[$categoryId])) {
                $categories[$categoryId] = [
                    'name'      => $expense->category->name,
                    'color'     => $expense->category->color,
                    'total_raw' => 0,
                    'count'     => 0,
                ];
            }

            $categories[$categoryId]['total_raw'] += $expense->amount;
            $categories[$categoryId]['count']++;
        }

        $this->formatTotalsAndPercentages($categories, $totalAmount, $formatter);

        // Sort by total_raw descending
        usort($categories, fn ($a, $b) => $b['total_raw'] <=> $a['total_raw']);

        return array_values($categories);
    }

    /**
     * @param  Collection<int, Income>  $incomes
     */
    protected function buildIncomeSources(Collection $incomes, Money $total, IntlMoneyFormatter $formatter): array
    {
        $sources = [];
        $totalAmount = (int) $total->getAmount();

        foreach ($incomes as $income) {
            $source = $income->source;

            if (! isset($sources[$source])) {
                $sources[$source] = [
                    'source'    => $source,
                    'total_raw' => 0,
                    'count'     => 0,
                ];
            }

            $sources[$source]['total_raw'] += $income->amount;
            $sources[$source]['count']++;
        }

        $this->formatTotalsAndPercentages($sources, $totalAmount, $formatter);

        // Sort by total_raw descending
        usort($sources, fn ($a, $b) => $b['total_raw'] <=> $a['total_raw']);

        return array_values($sources);
    }

    protected function formatTotalsAndPercentages(array &$items, int $totalAmount, IntlMoneyFormatter $formatter): void
    {
        foreach ($items as &$item) {
            $item['total'] = $formatter->format(Money::USD($item['total_raw']));
            $item['percentage'] = $totalAmount > 0
                ? round(($item['total_raw'] / $totalAmount) * 100, 1)
                : 0;
        }
    }

    /**
     * @param  Collection<int, Expense>  $expenses
     * @param  Collection<int, Income>  $incomes
     */
    protected function buildStats(Collection $expenses, Collection $incomes, Money $expensesTotal, Money $incomesTotal, string $from, string $to, IntlMoneyFormatter $formatter): array
    {
        $fromDate = Carbon::parse($from);
        $toDate = Carbon::parse($to);
        $days = $fromDate->diffInDays($toDate) + 1;

        // Average daily spent/earned
        $avgDailySpent = $days > 0 ? (int) $expensesTotal->getAmount() / $days : 0;
        $avgDailyEarned = $days > 0 ? (int) $incomesTotal->getAmount() / $days : 0;

        // Most frequent category
        $categoryCounts = [];
        foreach ($expenses as $expense) {
            $categoryName = $expense->category->name;
            $categoryCounts[$categoryName] = ($categoryCounts[$categoryName] ?? 0) + 1;
        }
        arsort($categoryCounts);
        $mostFrequentCategory = array_key_first($categoryCounts);
        $mostFrequentCategoryCount = $mostFrequentCategory ? $categoryCounts[$mostFrequentCategory] : 0;

        // Largest expense
        $largestExpense = null;
        if ($expenses->isNotEmpty()) {
            $largest = $expenses->sortByDesc('amount')->first();
            $largestExpense = [
                'amount'   => $formatter->format(Money::USD($largest->amount)),
                'category' => $largest->category->name,
            ];
        }

        // Savings rate: ((income - expenses) / income) * 100
        $incomeAmount = (int) $incomesTotal->getAmount();
        $expenseAmount = (int) $expensesTotal->getAmount();
        $savingsRate = null;
        if ($incomeAmount > 0) {
            $savingsRate = round((($incomeAmount - $expenseAmount) / $incomeAmount) * 100, 1);
        }

        return [
            'avg_daily_spent'              => $formatter->format(Money::USD((int) round($avgDailySpent))),
            'avg_daily_earned'             => $formatter->format(Money::USD((int) round($avgDailyEarned))),
            'most_frequent_category'       => $mostFrequentCategory,
            'most_frequent_category_count' => $mostFrequentCategoryCount,
            'largest_expense'              => $largestExpense,
            'savings_rate'                 => $savingsRate,
        ];
    }

    public function getAnnualSummary(User $user, string $year): array
    {
        // set to January 15th to avoid edge cases when using carbon addMonths()
        $date = Carbon::parse(sprintf('%s-01-15', $year));

        return [
            'summary' => $this->buildYearlyTotal($user, $date, $year),
            'months'  => $this->buildMonthlyTotals($user, $date),
        ];
    }

    protected function buildYearlyTotal(User $user, Carbon $date, string $year): array
    {
        $label = ['label' => $year];
        $totals = $this->buildSummary($user, $date->copy()->startOfYear(), $date->copy()->endOfYear());

        return array_merge($label, $totals);
    }

    protected function buildMonthlyTotals(User $user, Carbon $date): array
    {
        $totals = [];

        for ($i = 0; $i < 12; $i++) {
            $month = $i === 0 ? $date : $date->copy()->addMonths($i);

            $label = ['label' => $month->format('F Y')];
            $total = $this->buildSummary($user, $month->copy()->startOfMonth(), $month->copy()->endOfMonth());

            $totals[] = array_merge($label, $total);
        }

        return $totals;
    }

    protected function buildSummary(User $user, Carbon $from, Carbon $to): array
    {
        $dateRange = [$from->format('Y-m-d'), $to->format('Y-m-d')];
        $formatter = app(IntlMoneyFormatter::class);

        $expenses = $user->expenses()->with('category')->whereBetween('effective_date', $dateRange)->get();
        /** @var Money $expensesTotal */
        $expensesTotal = $expenses->reduce(
            fn (Money $carry, Expense $item) => $carry->add(Money::USD($item->amount)),
            Money::USD(0)
        );

        $incomes = $user->incomes()->whereBetween('effective_date', $dateRange)->get();
        /** @var Money $incomesTotal */
        $incomesTotal = $incomes->reduce(
            fn (Money $carry, Income $item) => $carry->add(Money::USD($item->amount)),
            Money::USD(0)
        );

        $totalDifference = $incomesTotal->subtract($expensesTotal);
        $isLoss = $incomesTotal->lessThan($expensesTotal);

        return [
            'total_expenses'   => $formatter->format($expensesTotal),
            'total_income'     => $formatter->format($incomesTotal),
            'total_difference' => $formatter->format($totalDifference),
            'is_loss'          => $isLoss,
            'count'            => $expenses->count(),
            'date_from'        => $dateRange[0],
            'date_to'          => $dateRange[1],
        ];
    }
}
