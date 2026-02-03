<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Carbon\Carbon;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;

class ExpenseService
{
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

        $expenses = $user->expenses()->with('category')->whereBetween('effective_date', $dateRange)->get();

        $expenses_total = $expenses->reduce(function (Money $carry, Expense $item) {
            return $carry->add(Money::USD($item->amount));
        }, Money::USD(0));

        $incomes = $user->incomes()->whereBetween('effective_date', $dateRange)->get();

        $incomes_total = $incomes->reduce(function (Money $carry, Income $item) {
            return $carry->add(Money::USD($item->amount));
        }, Money::USD(0));

        $total_difference = $incomes_total->subtract($expenses_total);
        $isLoss = $incomes_total->lessThan($expenses_total);

        return [
            'total_expenses'   => app(IntlMoneyFormatter::class)->format($expenses_total),
            'total_income'     => app(IntlMoneyFormatter::class)->format($incomes_total),
            'total_difference' => app(IntlMoneyFormatter::class)->format($total_difference),
            'is_loss'          => $isLoss,
            'count'            => $expenses->count(),
            'date_from'        => $dateRange[0],
            'date_to'          => $dateRange[1],
        ];
    }
}
