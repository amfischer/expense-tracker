<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $reports = [
            array_merge(['label' => '2024'], $user->getExpenseSummary(now()->startOfYear(), now()->endOfYear())),
            array_merge(['label' => now()->format('F')], $user->getExpenseSummary(now()->startOfMonth(), now()->endOfMonth())),
            array_merge(['label' => now()->subMonth()->format('F')], $user->getExpenseSummary(now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth())),
            array_merge(['label' => now()->subMonths(2)->format('F')], $user->getExpenseSummary(now()->subMonths(2)->startOfMonth(), now()->subMonths(2)->endOfMonth())),
        ];

        return Inertia::render('Dashboard', compact('reports'));
    }
}
