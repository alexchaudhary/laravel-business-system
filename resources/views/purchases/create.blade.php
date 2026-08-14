```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Add Purchase
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form method="POST" action="{{ route('purchases.store') }}">
                        @csrf

                        {{-- Purchase Information --}}
                        <h3 class="text-lg font-semibold mb-4">
                            Purchase Information
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

                            {{-- Supplier --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Supplier
                                </label>

                                <select
                                    name="supplier_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="">Select Supplier</option>

                                    @foreach ($suppliers as $supplier)
                                        <option
                                            value="{{ $supplier->id }}"
                                            {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}
                                        >
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Invoice Number --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Invoice Number
                                </label>

                                <input
                                    type="text"
                                    name="invoice_number"
                                    value="{{ old('invoice_number') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    required
                                >
                            </div>

                            {{-- Purchase Date --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Purchase Date
                                </label>

                                <input
                                    type="date"
                                    name="purchase_date"
                                    value="{{ old('purchase_date', date('Y-m-d')) }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    required
                                >
                            </div>

                            {{-- Status --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Status
                                </label>

                                <select
                                    name="status"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="pending"
                                        {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>
                                        Pending
                                    </option>

                                    <option value="received"
                                        {{ old('status') == 'received' ? 'selected' : '' }}>
                                        Received
                                    </option>

                                    <option value="cancelled"
                                        {{ old('status') == 'cancelled' ? 'selected' : '' }}>
                                        Cancelled
                                    </option>
                                </select>
                            </div>

                        </div>


                        {{-- Purchase Items --}}
                        <h3 class="text-lg font-semibold mb-4">
                            Purchase Items
                        </h3>

                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr class="border-b bg-gray-50">
                                        <th class="text-left p-3">Product</th>
                                        <th class="text-left p-3">Quantity</th>
                                        <th class="text-left p-3">Unit Price</th>
                                        <th class="text-left p-3">Total</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr class="border-b">

                                        {{-- Product --}}
                                        <td class="p-3">
                                            <select
                                                name="items[0][product_id]"
                                                class="w-full border-gray-300 rounded-md"
                                                required
                                            >
                                                <option value="">
                                                    Select Product
                                                </option>

                                                @foreach ($products as $product)
                                                    <option
                                                        value="{{ $product->id }}"
                                                        {{ old('items.0.product_id') == $product->id ? 'selected' : '' }}
                                                    >
                                                        {{ $product->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>

                                        {{-- Quantity --}}
                                        <td class="p-3">
                                            <input
                                                id="quantity"
                                                type="number"
                                                name="items[0][quantity]"
                                                value="{{ old('items.0.quantity', 1) }}"
                                                min="0.01"
                                                step="0.01"
                                                class="w-full border-gray-300 rounded-md"
                                                required
                                            >
                                        </td>

                                        {{-- Unit Price --}}
                                        <td class="p-3">
                                            <input
                                                id="unit_price"
                                                type="number"
                                                name="items[0][unit_price]"
                                                value="{{ old('items.0.unit_price', 0) }}"
                                                min="0"
                                                step="0.01"
                                                class="w-full border-gray-300 rounded-md"
                                                required
                                            >
                                        </td>

                                        {{-- Total --}}
                                        <td class="p-3">
                                            <input
                                                id="item_total"
                                                type="number"
                                                name="items[0][total]"
                                                value="{{ old('items.0.total', 0) }}"
                                                min="0"
                                                step="0.01"
                                                class="w-full border-gray-300 rounded-md bg-gray-100"
                                                readonly
                                            >
                                        </td>

                                    </tr>
                                </tbody>
                            </table>
                        </div>


                        {{-- Total Amount --}}
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700">
                                Total Amount
                            </label>

                            <input
                                id="total_amount"
                                type="number"
                                name="total_amount"
                                value="{{ old('total_amount', 0) }}"
                                min="0"
                                step="0.01"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100"
                                readonly
                            >
                        </div>


                        {{-- Notes --}}
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">
                                Notes
                            </label>

                            <textarea
                                name="notes"
                                rows="4"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            >{{ old('notes') }}</textarea>
                        </div>


                        {{-- Buttons --}}
                        <div class="mt-6 flex gap-3">

                            <a
                                href="{{ route('purchases.index') }}"
                                class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md"
                            >
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="px-4 py-2 bg-gray-800 text-white rounded-md"
                            >
                                Save Purchase
                            </button>

                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>


    {{-- Automatic Calculation --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const quantity = document.getElementById('quantity');
            const unitPrice = document.getElementById('unit_price');
            const itemTotal = document.getElementById('item_total');
            const totalAmount = document.getElementById('total_amount');

            function calculateTotal() {

                const qty = parseFloat(quantity.value) || 0;
                const price = parseFloat(unitPrice.value) || 0;

                const total = qty * price;

                itemTotal.value = total.toFixed(2);
                totalAmount.value = total.toFixed(2);
            }

            quantity.addEventListener('input', calculateTotal);
            unitPrice.addEventListener('input', calculateTotal);

            calculateTotal();
        });
    </script>

</x-app-layout>
```
