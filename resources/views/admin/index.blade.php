<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Admin Panel
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Manage and monitor your business system from one place.
            </p>
        </div>
    </x-slot>

    <div class="py-8">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Welcome to Admin Panel
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        You are logged in as an administrator.
                    </p>
                </div>
            </div>


            {{-- Statistics --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Users --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">
                        Users
                    </p>

                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        {{ $stats['users'] }}
                    </p>

                    <a
                        href="{{ route('users.index') }}"
                        class="inline-block mt-3 text-sm font-medium text-indigo-600 hover:text-indigo-800"
                    >
                        Manage Users →
                    </a>
                </div>


                {{-- Customers --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">
                        Customers
                    </p>

                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        {{ $stats['customers'] }}
                    </p>

                    <a
                        href="{{ route('customers.index') }}"
                        class="inline-block mt-3 text-sm font-medium text-indigo-600 hover:text-indigo-800"
                    >
                        View Customers →
                    </a>
                </div>


                {{-- Products --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">
                        Products
                    </p>

                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        {{ $stats['products'] }}
                    </p>

                    <a
                        href="{{ route('products.index') }}"
                        class="inline-block mt-3 text-sm font-medium text-indigo-600 hover:text-indigo-800"
                    >
                        View Products →
                    </a>
                </div>


                {{-- Suppliers --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">
                        Suppliers
                    </p>

                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        {{ $stats['suppliers'] }}
                    </p>

                    <a
                        href="{{ route('suppliers.index') }}"
                        class="inline-block mt-3 text-sm font-medium text-indigo-600 hover:text-indigo-800"
                    >
                        View Suppliers →
                    </a>
                </div>


                {{-- Purchases --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">
                        Purchases
                    </p>

                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        {{ $stats['purchases'] }}
                    </p>

                    <a
                        href="{{ route('purchases.index') }}"
                        class="inline-block mt-3 text-sm font-medium text-indigo-600 hover:text-indigo-800"
                    >
                        View Purchases →
                    </a>
                </div>


                {{-- Sales --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">
                        Sales
                    </p>

                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        {{ $stats['sales'] }}
                    </p>

                    <a
                        href="{{ route('sales.index') }}"
                        class="inline-block mt-3 text-sm font-medium text-indigo-600 hover:text-indigo-800"
                    >
                        View Sales →
                    </a>
                </div>


                {{-- Invoices --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">
                        Invoices
                    </p>

                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        {{ $stats['invoices'] }}
                    </p>

                    <a
                        href="{{ route('invoices.index') }}"
                        class="inline-block mt-3 text-sm font-medium text-indigo-600 hover:text-indigo-800"
                    >
                        View Invoices →
                    </a>
                </div>


                {{-- Payments --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">
                        Payments
                    </p>

                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        {{ $stats['payments'] }}
                    </p>

                    <a
                        href="{{ route('payments.index') }}"
                        class="inline-block mt-3 text-sm font-medium text-indigo-600 hover:text-indigo-800"
                    >
                        View Payments →
                    </a>
                </div>


                {{-- Expenses --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">
                        Expenses
                    </p>

                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        {{ $stats['expenses'] }}
                    </p>

                    <a
                        href="{{ route('expenses.index') }}"
                        class="inline-block mt-3 text-sm font-medium text-indigo-600 hover:text-indigo-800"
                    >
                        View Expenses →
                    </a>
                </div>

            </div>


            {{-- Recent Sales --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">

                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Recent Sales
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Latest sales transactions.
                        </p>
                    </div>

                    <a
                        href="{{ route('sales.index') }}"
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                    >
                        View All
                    </a>
                </div>

                @if ($recentSales->count())

                    <div class="overflow-x-auto">

                        <table class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-gray-50">

                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Invoice
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Customer
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Date
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Amount
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Status
                                    </th>
                                </tr>

                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">

                                @foreach ($recentSales as $sale)

                                    <tr>

                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                            {{ $sale->invoice_number }}
                                        </td>

                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $sale->customer?->name ?? 'Unknown' }}
                                        </td>

                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $sale->sale_date?->format('Y-m-d') }}
                                        </td>

                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            Rs. {{ number_format((float) $sale->total_amount, 2) }}
                                        </td>

                                        <td class="px-6 py-4 text-sm">
                                            {{ ucfirst($sale->status) }}
                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <div class="p-6 text-sm text-gray-500">
                        No sales found.
                    </div>

                @endif

            </div>


            {{-- Recent Invoices --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">

                <div class="p-6 border-b border-gray-200 flex items-center justify-between">

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Recent Invoices
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Latest invoice records.
                        </p>
                    </div>

                    <a
                        href="{{ route('invoices.index') }}"
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                    >
                        View All
                    </a>

                </div>

                @if ($recentInvoices->count())

                    <div class="overflow-x-auto">

                        <table class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-gray-50">

                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Invoice
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Customer
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Date
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Amount
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Status
                                    </th>
                                </tr>

                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">

                                @foreach ($recentInvoices as $invoice)

                                    <tr>

                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                            {{ $invoice->invoice_number }}
                                        </td>

                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $invoice->sale?->customer?->name ?? 'Unknown' }}
                                        </td>

                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $invoice->invoice_date?->format('Y-m-d') }}
                                        </td>

                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            Rs. {{ number_format((float) $invoice->total_amount, 2) }}
                                        </td>

                                        <td class="px-6 py-4 text-sm">
                                            {{ ucfirst(str_replace('_', ' ', $invoice->status)) }}
                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <div class="p-6 text-sm text-gray-500">
                        No invoices found.
                    </div>

                @endif

            </div>


            {{-- Recent Payments --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">

                <div class="p-6 border-b border-gray-200 flex items-center justify-between">

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Recent Payments
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Latest payment records.
                        </p>
                    </div>

                    <a
                        href="{{ route('payments.index') }}"
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                    >
                        View All
                    </a>

                </div>

                @if ($recentPayments->count())

                    <div class="overflow-x-auto">

                        <table class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-gray-50">

                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Payment
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Invoice
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Date
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Amount
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Method
                                    </th>
                                </tr>

                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">

                                @foreach ($recentPayments as $payment)

                                    <tr>

                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                            {{ $payment->payment_number }}
                                        </td>

                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $payment->invoice?->invoice_number ?? 'Unknown' }}
                                        </td>

                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $payment->payment_date?->format('Y-m-d') }}
                                        </td>

                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            Rs. {{ number_format((float) $payment->amount, 2) }}
                                        </td>

                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <div class="p-6 text-sm text-gray-500">
                        No payments found.
                    </div>

                @endif

            </div>

        </div>

    </div>

</x-app-layout>
