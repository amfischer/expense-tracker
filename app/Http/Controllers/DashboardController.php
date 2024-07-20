<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $reports = [
            array_merge(['label' => '2024'], $user->getExpenseSummary(now()->startOfYear(), now()->endOfYear())),
            array_merge(['label' => now()->format('F')], $user->getExpenseSummary(now()->startOfMonth(), now()->endOfMonth())),
        ];

        $currentMonth = now()->month;

        for ($i = 1; $i < $currentMonth; $i++) {
            $reports[] = array_merge(
                ['label' => now()->subMonths($i)->format('F')],
                $user->getExpenseSummary(now()->subMonths($i)->startOfMonth(), now()->subMonths($i)->endOfMonth())
            );
        }

        return Inertia::render('Dashboard/Index', compact('reports'));
    }
}
