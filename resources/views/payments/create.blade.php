<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Payment
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Record a payment received against an invoice.
            </p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg">

                {{-- Header --}}
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Payment Information
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        Enter the payment details below.
                    </p>
                </div>

                {{-- Form --}}
                <form method="POST" action="{{ route('payments.store') }}">
                    @csrf

                    <div class="p-6 space-y-6">

                        {{-- Invoice --}}
                        <div>
                            <label
                                for="invoice_id"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Invoice
                            </label>

                            <select
                                id="invoice_id"
                                name="invoice_id"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">
                                    Select Invoice
                                </option>

                                @foreach ($invoices as $invoice)
                                    <option
                                        value="{{ $invoice->id }}"
                                        {{ old('invoice_id') == $invoice->id ? 'selected' : '' }}
                                    >
                                        {{ $invoice->invoice_number }}
                                        —
                                        {{ $invoice->sale?->customer?->name ?? 'Unknown Customer' }}
                                        —
                                        Rs. {{ number_format($invoice->total_amount, 2) }}
                                    </option>
                                @endforeach
                            </select>

                            @error('invoice_id')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>


                        {{-- Payment Number --}}
                        <div>
                            <label
                                for="payment_number"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Payment Number
                            </label>

                            <input
                                id="payment_number"
                                name="payment_number"
                                type="text"
                                value="{{ old('payment_number', 'PAY-0001') }}"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >

                            @error('payment_number')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>


                        {{-- Date + Amount --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Payment Date --}}
                            <div>
                                <label
                                    for="payment_date"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Payment Date
                                </label>

                                <input
                                    id="payment_date"
                                    name="payment_date"
                                    type="date"
                                    value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                @error('payment_date')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>


                            {{-- Amount --}}
                            <div>
                                <label
                                    for="amount"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Amount
                                </label>

                                <input
                                    id="amount"
                                    name="amount"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    value="{{ old('amount', '0.00') }}"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                @error('amount')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                        </div>


                        {{-- Payment Method --}}
                        <div>
                            <label
                                for="payment_method"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Payment Method
                            </label>

                            <select
                                id="payment_method"
                                name="payment_method"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="cash"
                                    {{ old('payment_method', 'cash') === 'cash' ? 'selected' : '' }}>
                                    Cash
                                </option>

                                <option value="bank_transfer"
                                    {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>
                                    Bank Transfer
                                </option>

                                <option value="card"
                                    {{ old('payment_method') === 'card' ? 'selected' : '' }}>
                                    Card
                                </option>

                                <option value="mobile_payment"
                                    {{ old('payment_method') === 'mobile_payment' ? 'selected' : '' }}>
                                    Mobile Payment
                                </option>

                                <option value="other"
                                    {{ old('payment_method') === 'other' ? 'selected' : '' }}>
                                    Other
                                </option>
                            </select>

                            @error('payment_method')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>


                        {{-- Reference Number --}}
                        <div>
                            <label
                                for="reference_number"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Reference Number
                            </label>

                            <input
                                id="reference_number"
                                name="reference_number"
                                type="text"
                                value="{{ old('reference_number') }}"
                                placeholder="Optional transaction/reference number"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >

                            @error('reference_number')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>


                        {{-- Notes --}}
                        <div>
                            <label
                                for="notes"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Notes
                            </label>

                            <textarea
                                id="notes"
                                name="notes"
                                rows="4"
                                placeholder="Optional payment notes..."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >{{ old('notes') }}</textarea>

                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>


                    {{-- Actions --}}
                    <div
                        style="
                            display: flex;
                            justify-content: flex-end;
                            align-items: center;
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
                            Cancel
                        </a>

                        <button
                            type="submit"
                            style="
                                display: inline-flex !important;
                                align-items: center;
                                justify-content: center;
                                visibility: visible !important;
                                opacity: 1 !important;
                                position: relative;
                                z-index: 50;
                                padding: 10px 20px;
                                background: #4f46e5 !important;
                                color: #ffffff !important;
                                border: 1px solid #4f46e5;
                                border-radius: 6px;
                                font-weight: 600;
                                font-size: 14px;
                                cursor: pointer;
                            "
                        >
                            Create Payment
                        </button>

                    </div>

                </form>

            </div>

        </div>
    </div>

</x-app-layout>