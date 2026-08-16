<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Payments
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Manage customer invoice payments and payment records.
            </p>
        </div>
    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if (session('success'))
                <div class="mb-6 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Header / Create Button --}}
            <div class="mb-6 flex items-center justify-between">

                <div>
                    <h3 class="text-lg font-semibold text-gray-800">
                        Payment Records
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        View and manage all recorded payments.
                    </p>
                </div>

                <a
                    href="{{ route('payments.create') }}"
                    class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-md font-semibold text-sm hover:bg-indigo-700 transition"
                >
                    + Create Payment
                </a>

            </div>


            {{-- Payments Table --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">

                            <tr>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Payment #
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Invoice
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Customer
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Payment Date
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Amount
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Method
                                </th>

                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody class="bg-white divide-y divide-gray-200">

                            @forelse ($payments as $payment)

                                <tr class="hover:bg-gray-50 transition">

                                    {{-- Payment Number --}}
                                    <td class="px-6 py-4 whitespace-nowrap">

                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $payment->payment_number }}
                                        </div>

                                    </td>


                                    {{-- Invoice --}}
                                    <td class="px-6 py-4 whitespace-nowrap">

                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $payment->invoice?->invoice_number ?? '—' }}
                                        </div>

                                    </td>


                                    {{-- Customer --}}
                                    <td class="px-6 py-4 whitespace-nowrap">

                                        <div class="text-sm text-gray-900">
                                            {{ $payment->invoice?->sale?->customer?->name ?? '—' }}
                                        </div>

                                    </td>


                                    {{-- Payment Date --}}
                                    <td class="px-6 py-4 whitespace-nowrap">

                                        <div class="text-sm text-gray-700">
                                            {{ $payment->payment_date?->format('M d, Y') ?? '—' }}
                                        </div>

                                    </td>


                                    {{-- Amount --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right">

                                        <div class="text-sm font-semibold text-gray-900">
                                            Rs. {{ number_format((float) $payment->amount, 2) }}
                                        </div>

                                    </td>


                                    {{-- Payment Method --}}
                                    <td class="px-6 py-4 whitespace-nowrap">

                                        @php
                                            $methodLabels = [
                                                'cash' => 'Cash',
                                                'bank_transfer' => 'Bank Transfer',
                                                'card' => 'Card',
                                                'mobile_payment' => 'Mobile Payment',
                                                'other' => 'Other',
                                            ];
                                        @endphp

                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                            {{ $methodLabels[$payment->payment_method] ?? ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                        </span>

                                    </td>


                                    {{-- Actions --}}
                                    <td class="px-6 py-4">

                                        <div class="flex items-center justify-center gap-2">

                                            {{-- View --}}
                                            <a
                                                href="{{ route('payments.show', $payment) }}"
                                                class="inline-flex items-center justify-center h-10 px-4 bg-gray-100 text-gray-700 rounded-md font-medium text-sm hover:bg-gray-200 transition"
                                            >
                                                View
                                            </a>


                                            {{-- Edit --}}
                                            <a
                                                href="{{ route('payments.edit', $payment) }}"
                                                class="inline-flex items-center justify-center h-10 px-4 bg-indigo-600 text-white rounded-md font-medium text-sm hover:bg-indigo-700 transition"
                                            >
                                                Edit
                                            </a>


                                            {{-- Delete --}}
                                            <form
                                                method="POST"
                                                action="{{ route('payments.destroy', $payment) }}"
                                                onsubmit="return confirm('Are you sure you want to delete this payment?');"
                                                class="m-0"
                                            >

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center justify-center h-10 px-4 bg-red-600 text-white rounded-md font-medium text-sm hover:bg-red-700 transition"
                                                >
                                                    Delete
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="7"
                                        class="px-6 py-12 text-center"
                                    >

                                        <div class="text-sm font-medium text-gray-500">
                                            No payments found.
                                        </div>

                                        <p class="text-sm text-gray-400 mt-1">
                                            Create your first payment to see it here.
                                        </p>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>