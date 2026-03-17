<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Services\ExpenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request, ExpenseService $expenseService): Response
    {
        $totals = [
            $expenseService->getAnnualSummary($request->user(), '2026'),
            $expenseService->getAnnualSummary($request->user(), '2025'),
            $expenseService->getAnnualSummary($request->user(), '2024'),
        ];

        $paymentMethods = PaymentMethod::HTMLSelectOptions();

        return Inertia::render('Dashboard/Index', compact('totals', 'paymentMethods'));
    }

    public function getSummaryDetails(Request $request, ExpenseService $expenseService): JsonResponse
    {
        $validated = $request->validate([
            'date_from' => 'required|date_format:Y-m-d',
            'date_to'   => 'required|date_format:Y-m-d',
        ]);

        $details = $expenseService->getDetailedSummary(
            $request->user(),
            $validated['date_from'],
            $validated['date_to']
        );

        return response()->json($details);
    }
}
