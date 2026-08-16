<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Payment Details
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                View payment and invoice information.
            </p>
        </div>
    </x-slot>

    <div class="py-12">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg">

                {{-- Header --}}
                <div class="p-6 border-b border-gray-200 flex justify-between items-start">

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Payment Information
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Payment #{{ $payment->payment_number }}
                        </p>
                    </div>

                </div>


                {{-- Payment Details --}}
                <div class="p-6 space-y-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Payment Number --}}
                        <div>
                            <p class="text-sm text-gray-500">
                                Payment Number
                            </p>

                            <p class="mt-1 font-semibold text-gray-800">
                                {{ $payment->payment_number }}
                            </p>
                        </div>


                        {{-- Invoice --}}
                        <div>
                            <p class="text-sm text-gray-500">
                                Invoice
                            </p>

                            <p class="mt-1 font-semibold text-gray-800">
                                {{ $payment->invoice?->invoice_number ?? '—' }}
                            </p>
                        </div>


                        {{-- Customer --}}
                        <div>
                            <p class="text-sm text-gray-500">
                                Customer
                            </p>

                            <p class="mt-1 font-semibold text-gray-800">
                                {{ $payment->invoice?->sale?->customer?->name ?? '—' }}
                            </p>
                        </div>


                        {{-- Payment Date --}}
                        <div>
                            <p class="text-sm text-gray-500">
                                Payment Date
                            </p>

                            <p class="mt-1 font-semibold text-gray-800">
                                {{ $payment->payment_date?->format('d M Y') ?? '—' }}
                            </p>
                        </div>


                        {{-- Amount --}}
                        <div>
                            <p class="text-sm text-gray-500">
                                Amount
                            </p>

                            <p class="mt-1 font-semibold text-indigo-600">
                                Rs. {{ number_format($payment->amount, 2) }}
                            </p>
                        </div>


                        {{-- Payment Method --}}
                        <div>
                            <p class="text-sm text-gray-500">
                                Payment Method
                            </p>

                            <p class="mt-1 font-semibold text-gray-800 capitalize">
                                {{ str_replace('_', ' ', $payment->payment_method) }}
                            </p>
                        </div>


                        {{-- Reference Number --}}
                        <div>
                            <p class="text-sm text-gray-500">
                                Reference Number
                            </p>

                            <p class="mt-1 font-semibold text-gray-800">
                                {{ $payment->reference_number ?? '—' }}
                            </p>
                        </div>

                    </div>


                    {{-- Notes --}}
                    <div class="border-t border-gray-200 pt-6">

                        <p class="text-sm text-gray-500">
                            Notes
                        </p>

                        <p class="mt-2 text-gray-800">
                            {{ $payment->notes ?? '—' }}
                        </p>

                    </div>

                </div>


                {{-- Actions --}}
                <div
                    style="
                        display: flex;
                        justify-content: flex-end;
                        gap: 12px;
                        padding: 20px 24px;
                        background: #f9fafb;
                        border-top: 1px solid #e5e7eb;
                    "
                >

                    <a
                        href="{{ route('payments.index') }}"
                        style="
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            padding: 10px 20px;
                            background: #e5e7eb;
                            color: #374151;
                            border: 1px solid #d1d5db;
                            border-radius: 6px;
                            font-weight: 600;
                            font-size: 14px;
                            text-decoration: none;
                        "
                    >
                        Back
                    </a>


                    <a
                        href="{{ route('payments.edit', $payment) }}"
                        style="
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            padding: 10px 20px;
                            background: #4f46e5;
                            color: #ffffff;
                            border: 1px solid #4f46e5;
                            border-radius: 6px;
                            font-weight: 600;
                            font-size: 14px;
                            text-decoration: none;
                        "
                    >
                        Edit Payment
                    </a>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>