<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        $reports = [
            array_merge(['label' => '2024'], $user->getExpenseSummary(now()->startOfYear(), now()->endOfYear())),
            array_merge(['label' => now()->format('F Y')], $user->getExpenseSummary(now()->startOfMonth(), now()->endOfMonth())),
        ];

        $currentMonth = now()->month;

        for ($i = 1; $i < $currentMonth; $i++) {
            $reports[] = array_merge(
                ['label' => now()->subMonthsWithoutOverflow($i)->format('F Y')],
                $user->getExpenseSummary(now()->subMonthsWithoutOverflow($i)->startOfMonth(), now()->subMonthsWithoutOverflow($i)->endOfMonth())
            );
        }

        $paymentMethods = PaymentMethod::HTMLSelectOptions();

        return Inertia::render('Dashboard/Index', compact('reports', 'paymentMethods'));
    }

    public function getSummaryDetails(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date_from' => 'required|date_format:Y-m-d',
            'date_to'   => 'required|date_format:Y-m-d',
        ]);

        /** @var User $user */
        $user = $request->user();

        $details = $user->getExpenseSummaryDetails($validated['date_from'], $validated['date_to']);

        return response()->json($details);
    }
}
