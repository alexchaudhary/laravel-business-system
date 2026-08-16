<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Sale;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index()
    {
        $invoices = Invoice::with([
            'sale.customer',
        ])
            ->latest('invoice_date')
            ->latest('id')
            ->get();

        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create()
    {
        $sales = Sale::with('customer')
            ->latest('sale_date')
            ->get();

        return view('invoices.create', compact('sales'));
    }

    /**
     * Store a newly created invoice.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_id' => [
                'required',
                'exists:sales,id',
            ],

            'invoice_number' => [
                'required',
                'string',
                'max:255',
                'unique:invoices,invoice_number',
            ],

            'invoice_date' => [
                'required',
                'date',
            ],

            'due_date' => [
                'nullable',
                'date',
                'after_or_equal:invoice_date',
            ],

            'subtotal' => [
                'required',
                'numeric',
                'min:0',
            ],

            'discount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'tax' => [
                'required',
                'numeric',
                'min:0',
            ],

            'total_amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'status' => [
                'required',
                'in:draft,issued,paid,partially_paid,overdue,cancelled',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        Invoice::create($validated);

        return redirect()
            ->route('invoices.index')
            ->with(
                'success',
                'Invoice created successfully.'
            );
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load([
            'sale.customer',
            'sale.items.product',
        ]);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(Invoice $invoice)
    {
        $invoice->load('sale.customer');

        $sales = Sale::with('customer')
            ->latest('sale_date')
            ->get();

        return view(
            'invoices.edit',
            compact('invoice', 'sales')
        );
    }

    /**
     * Update the specified invoice.
     */
    public function update(
        Request $request,
        Invoice $invoice
    ) {
        $validated = $request->validate([
            'sale_id' => [
                'required',
                'exists:sales,id',
            ],

            'invoice_number' => [
                'required',
                'string',
                'max:255',
                'unique:invoices,invoice_number,' . $invoice->id,
            ],

            'invoice_date' => [
                'required',
                'date',
            ],

            'due_date' => [
                'nullable',
                'date',
                'after_or_equal:invoice_date',
            ],

            'subtotal' => [
                'required',
                'numeric',
                'min:0',
            ],

            'discount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'tax' => [
                'required',
                'numeric',
                'min:0',
            ],

            'total_amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'status' => [
                'required',
                'in:draft,issued,paid,partially_paid,overdue,cancelled',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $invoice->update($validated);

        return redirect()
            ->route('invoices.show', $invoice)
            ->with(
                'success',
                'Invoice updated successfully.'
            );
    }

    /**
     * Remove the specified invoice.
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()
            ->route('invoices.index')
            ->with(
                'success',
                'Invoice deleted successfully.'
            );
    }
}
