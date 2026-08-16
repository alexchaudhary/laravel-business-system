<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'customers' => Customer::count(),
            'products' => Product::count(),
            'suppliers' => Supplier::count(),
            'purchases' => Purchase::count(),
            'sales' => Sale::count(),
            'invoices' => Invoice::count(),
            'payments' => Payment::count(),
            'expenses' => Expense::count(),
        ];

        $recentSales = Sale::with('customer')
            ->latest()
            ->take(5)
            ->get();

        $recentInvoices = Invoice::with('sale.customer')
            ->latest('invoice_date')
            ->latest('id')
            ->take(5)
            ->get();

        $recentPayments = Payment::with('invoice')
            ->latest('payment_date')
            ->latest('id')
            ->take(5)
            ->get();

        return view('admin.index', compact(
            'stats',
            'recentSales',
            'recentInvoices',
            'recentPayments'
        ));
    }
}
