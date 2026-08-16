<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Invoice Details
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                View invoice and customer billing information.
            </p>
        </div>
    </x-slot>

    <div class="py-12">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">

                {{-- Header --}}
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">

                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                Invoice Information
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Invoice #{{ $invoice->invoice_number }}
                            </p>
                        </div>

                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold
                            @if($invoice->status === 'paid')
                                bg-green-100 text-green-700
                            @elseif($invoice->status === 'issued')
                                bg-blue-100 text-blue-700
                            @elseif($invoice->status === 'overdue')
                                bg-red-100 text-red-700
                            @elseif($invoice->status === 'cancelled')
                                bg-gray-200 text-gray-700
                            @else
                                bg-yellow-100 text-yellow-700
                            @endif
                        ">
                            {{ ucfirst(str_replace('_', ' ', $invoice->status)) }}
                        </span>

                    </div>
                </div>


                {{-- Invoice Details --}}
                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Invoice Number --}}
                        <div>
                            <p class="text-sm text-gray-500">
                                Invoice Number
                            </p>

                            <p class="mt-1 font-semibold text-gray-800">
                                {{ $invoice->invoice_number }}
                            </p>
                        </div>


                        {{-- Customer --}}
                        <div>
                            <p class="text-sm text-gray-500">
                                Customer
                            </p>

                            <p class="mt-1 font-semibold text-gray-800">
                                {{ $invoice->sale?->customer?->name ?? 'N/A' }}
                            </p>
                        </div>


                        {{-- Invoice Date --}}
                        <div>
                            <p class="text-sm text-gray-500">
                                Invoice Date
                            </p>

                            <p class="mt-1 text-gray-800">
                                {{ $invoice->invoice_date?->format('d M Y') ?? 'N/A' }}
                            </p>
                        </div>


                        {{-- Due Date --}}
                        <div>
                            <p class="text-sm text-gray-500">
                                Due Date
                            </p>

                            <p class="mt-1 text-gray-800">
                                {{ $invoice->due_date?->format('d M Y') ?? '—' }}
                            </p>
                        </div>


                        {{-- Sale --}}
                        <div>
                            <p class="text-sm text-gray-500">
                                Related Sale
                            </p>

                            <p class="mt-1 text-gray-800">
                                Sale #{{ $invoice->sale_id }}
                            </p>
                        </div>


                        {{-- Status --}}
                        <div>
                            <p class="text-sm text-gray-500">
                                Status
                            </p>

                            <p class="mt-1 text-gray-800">
                                {{ ucfirst(str_replace('_', ' ', $invoice->status)) }}
                            </p>
                        </div>

                    </div>


                    {{-- Amounts --}}
                    <div class="mt-8 border-t border-gray-200 pt-6">

                        <h4 class="text-base font-semibold text-gray-800 mb-4">
                            Amount Details
                        </h4>

                        <div class="space-y-3">

                            <div class="flex justify-between">
                                <span class="text-gray-600">
                                    Subtotal
                                </span>

                                <span class="font-medium text-gray-800">
                                    Rs. {{ number_format((float) $invoice->subtotal, 2) }}
                                </span>
                            </div>


                            <div class="flex justify-between">
                                <span class="text-gray-600">
                                    Discount
                                </span>

                                <span class="font-medium text-gray-800">
                                    Rs. {{ number_format((float) $invoice->discount, 2) }}
                                </span>
                            </div>


                            <div class="flex justify-between">
                                <span class="text-gray-600">
                                    Tax
                                </span>

                                <span class="font-medium text-gray-800">
                                    Rs. {{ number_format((float) $invoice->tax, 2) }}
                                </span>
                            </div>


                            <div class="flex justify-between border-t border-gray-200 pt-3">

                                <span class="text-lg font-semibold text-gray-800">
                                    Total Amount
                                </span>

                                <span class="text-lg font-bold text-indigo-600">
                                    Rs. {{ number_format((float) $invoice->total_amount, 2) }}
                                </span>

                            </div>

                        </div>

                    </div>


                    {{-- Notes --}}
                    @if($invoice->notes)
                        <div class="mt-8 border-t border-gray-200 pt-6">

                            <h4 class="text-base font-semibold text-gray-800">
                                Notes
                            </h4>

                            <p class="mt-2 text-sm text-gray-600 whitespace-pre-line">
                                {{ $invoice->notes }}
                            </p>

                        </div>
                    @endif

                </div>


                {{-- Actions --}}
                <div class="flex justify-end items-center gap-3 px-6 py-4 bg-gray-50 border-t border-gray-200">

                    <a
                        href="{{ route('invoices.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md font-semibold text-sm hover:bg-gray-300"
                    >
                        Back
                    </a>

                    <a
                        href="{{ route('invoices.edit', $invoice) }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md font-semibold text-sm hover:bg-indigo-700"
                    >
                        Edit Invoice
                    </a>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>