<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\Sale;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
        ]);

        $startDate = $request->input('from_date');
        $endDate = $request->input('to_date');

        $salesQuery = Sale::query()
            ->where('status', '!=', 'cancelled');

        $purchasesQuery = Purchase::query()
            ->where('status', '!=', 'cancelled');

        $expensesQuery = Expense::query();

        if ($startDate) {
            $salesQuery->whereDate('sale_date', '>=', $startDate);
            $purchasesQuery->whereDate('purchase_date', '>=', $startDate);
            $expensesQuery->whereDate('expense_date', '>=', $startDate);
        }

        if ($endDate) {
            $salesQuery->whereDate('sale_date', '<=', $endDate);
            $purchasesQuery->whereDate('purchase_date', '<=', $endDate);
            $expensesQuery->whereDate('expense_date', '<=', $endDate);
        }

        $totalSales = $salesQuery->sum('total_amount');

        $totalPurchases = $purchasesQuery->sum('total_amount');

        $totalExpenses = $expensesQuery->sum('amount');

        $profit = $totalSales - $totalPurchases - $totalExpenses;

        return view('reports.index', compact(
            'totalSales',
            'totalPurchases',
            'totalExpenses',
            'profit',
            'startDate',
            'endDate'
        ));
    }
}

