<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $year = ['label' => '2024'];
        $year = array_merge($year, $user->getExpenseSummary(now()->startOfYear(), now()->endOfYear()));

        $month = ['label' => now()->format('F')];
        $month = array_merge($month, $user->getExpenseSummary(now()->startOfMonth(), now()->endOfMonth()));

        $lastMonth = ['label' => now()->subMonth()->format('F')];
        $lastMonth = array_merge($lastMonth, $user->getExpenseSummary(now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()));

        $twoMonthsAgo = ['label' => now()->subMonths(2)->format('F')];
        $twoMonthsAgo = array_merge($twoMonthsAgo, $user->getExpenseSummary(now()->subMonths(2)->startOfMonth(), now()->subMonths(2)->endOfMonth()));

        return Inertia::render('Dashboard', [
            'year'         => $year,
            'month'        => $month,
            'lastMonth'    => $lastMonth,
            'twoMonthsAgo' => $twoMonthsAgo,
        ]);
    }
}
