<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments.
     */
    public function index()
    {
        $payments = Payment::with([
            'invoice.sale.customer',
        ])
            ->latest('payment_date')
            ->latest('id')
            ->get();

        return view('payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create()
    {
        $invoices = Invoice::with('sale.customer')
            ->whereIn('status', [
                'issued',
                'partially_paid',
                'overdue',
            ])
            ->latest('invoice_date')
            ->get();

        return view('payments.create', compact('invoices'));
    }

    /**
     * Store a newly created payment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => [
                'required',
                'exists:invoices,id',
            ],

            'payment_number' => [
                'required',
                'string',
                'max:255',
                'unique:payments,payment_number',
            ],

            'payment_date' => [
                'required',
                'date',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'payment_method' => [
                'required',
                'in:cash,bank_transfer,card,mobile_payment,other',
            ],

            'reference_number' => [
                'nullable',
                'string',
                'max:255',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        Payment::create($validated);

        return redirect()
            ->route('payments.index')
            ->with(
                'success',
                'Payment created successfully.'
            );
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment)
    {
        $payment->load([
            'invoice.sale.customer',
        ]);

        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified payment.
     */
    public function edit(Payment $payment)
    {
        $payment->load('invoice.sale.customer');

        $invoices = Invoice::with('sale.customer')
            ->latest('invoice_date')
            ->get();

        return view(
            'payments.edit',
            compact('payment', 'invoices')
        );
    }

    /**
     * Update the specified payment.
     */
    public function update(
        Request $request,
        Payment $payment
    ) {
        $validated = $request->validate([
            'invoice_id' => [
                'required',
                'exists:invoices,id',
            ],

            'payment_number' => [
                'required',
                'string',
                'max:255',
                'unique:payments,payment_number,' . $payment->id,
            ],

            'payment_date' => [
                'required',
                'date',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'payment_method' => [
                'required',
                'in:cash,bank_transfer,card,mobile_payment,other',
            ],

            'reference_number' => [
                'nullable',
                'string',
                'max:255',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $payment->update($validated);

        return redirect()
            ->route('payments.show', $payment)
            ->with(
                'success',
                'Payment updated successfully.'
            );
    }

    /**
     * Remove the specified payment.
     */
    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()
            ->route('payments.index')
            ->with(
                'success',
                'Payment deleted successfully.'
            );
    }
}