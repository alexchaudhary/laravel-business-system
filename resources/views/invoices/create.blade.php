<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Invoice
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Create a new customer invoice and billing record.
            </p>
        </div>
    </x-slot>

    <div class="py-12">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg">

                {{-- Header --}}
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Invoice Information
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        Enter the invoice details below.
                    </p>
                </div>


                {{-- Form --}}
                <form method="POST" action="{{ route('invoices.store') }}">

                    @csrf

                    <div class="p-6 space-y-6">

                        {{-- Sale --}}
                        <div>
                            <label
                                for="sale_id"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Sale
                            </label>

                            <select
                                id="sale_id"
                                name="sale_id"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">
                                    Select Sale
                                </option>

                                @foreach ($sales as $sale)
                                    <option
                                        value="{{ $sale->id }}"
                                        data-total="{{ $sale->total_amount }}"
                                        {{ old('sale_id') == $sale->id ? 'selected' : '' }}
                                    >
                                        Sale #{{ $sale->id }}
                                        —
                                        {{ $sale->customer?->name ?? 'Unknown Customer' }}
                                        —
                                        Rs. {{ number_format($sale->total_amount, 2) }}
                                    </option>
                                @endforeach
                            </select>

                            @error('sale_id')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>


                        {{-- Invoice Number --}}
                        <div>
                            <label
                                for="invoice_number"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Invoice Number
                            </label>

                            <input
                                id="invoice_number"
                                name="invoice_number"
                                type="text"
                                value="{{ old('invoice_number') }}"
                                placeholder="INV-0001"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >

                            @error('invoice_number')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>


                        {{-- Dates --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Invoice Date --}}
                            <div>
                                <label
                                    for="invoice_date"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Invoice Date
                                </label>

                                <input
                                    id="invoice_date"
                                    name="invoice_date"
                                    type="date"
                                    value="{{ old('invoice_date', now()->format('Y-m-d')) }}"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                @error('invoice_date')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>


                            {{-- Due Date --}}
                            <div>
                                <label
                                    for="due_date"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Due Date
                                </label>

                                <input
                                    id="due_date"
                                    name="due_date"
                                    type="date"
                                    value="{{ old('due_date') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                @error('due_date')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                        </div>


                        {{-- Amounts --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Subtotal --}}
                            <div>
                                <label
                                    for="subtotal"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Subtotal
                                </label>

                                <input
                                    id="subtotal"
                                    name="subtotal"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    value="{{ old('subtotal', '0.00') }}"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                @error('subtotal')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>


                            {{-- Discount --}}
                            <div>
                                <label
                                    for="discount"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Discount
                                </label>

                                <input
                                    id="discount"
                                    name="discount"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    value="{{ old('discount', '0.00') }}"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                @error('discount')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>


                            {{-- Tax --}}
                            <div>
                                <label
                                    for="tax"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Tax
                                </label>

                                <input
                                    id="tax"
                                    name="tax"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    value="{{ old('tax', '0.00') }}"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                @error('tax')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>


                            {{-- Total --}}
                            <div>
                                <label
                                    for="total_amount"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Total Amount
                                </label>

                                <input
                                    id="total_amount"
                                    name="total_amount"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    value="{{ old('total_amount', '0.00') }}"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                @error('total_amount')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                        </div>


                        {{-- Status --}}
                        <div>
                            <label
                                for="status"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Status
                            </label>

                            <select
                                id="status"
                                name="status"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="draft"
                                    {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>
                                    Draft
                                </option>

                                <option value="issued"
                                    {{ old('status') === 'issued' ? 'selected' : '' }}>
                                    Issued
                                </option>

                                <option value="paid"
                                    {{ old('status') === 'paid' ? 'selected' : '' }}>
                                    Paid
                                </option>

                                <option value="partially_paid"
                                    {{ old('status') === 'partially_paid' ? 'selected' : '' }}>
                                    Partially Paid
                                </option>

                                <option value="overdue"
                                    {{ old('status') === 'overdue' ? 'selected' : '' }}>
                                    Overdue
                                </option>

                                <option value="cancelled"
                                    {{ old('status') === 'cancelled' ? 'selected' : '' }}>
                                    Cancelled
                                </option>
                            </select>

                            @error('status')
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
                                placeholder="Optional invoice notes..."
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
                            href="{{ route('invoices.index') }}"
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
                            Create Invoice
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    {{-- Auto-fill subtotal and total from selected sale --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const saleSelect = document.getElementById('sale_id');
            const subtotalInput = document.getElementById('subtotal');
            const discountInput = document.getElementById('discount');
            const taxInput = document.getElementById('tax');
            const totalInput = document.getElementById('total_amount');

            function calculateTotal() {

                const subtotal = parseFloat(subtotalInput.value) || 0;
                const discount = parseFloat(discountInput.value) || 0;
                const tax = parseFloat(taxInput.value) || 0;

                const total = subtotal - discount + tax;

                totalInput.value = total.toFixed(2);
            }

            saleSelect.addEventListener('change', function () {

                const selectedOption =
                    saleSelect.options[saleSelect.selectedIndex];

                const saleTotal =
                    parseFloat(selectedOption.dataset.total) || 0;

                subtotalInput.value = saleTotal.toFixed(2);

                calculateTotal();
            });

            discountInput.addEventListener('input', calculateTotal);
            taxInput.addEventListener('input', calculateTotal);

        });
    </script>

</x-app-layout>
