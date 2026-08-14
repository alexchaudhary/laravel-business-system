```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Sale
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded">
                            <ul class="list-disc ml-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('sales.update', $sale) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Customer
                                </label>

                                <select name="customer_id"
                                        class="mt-1 block w-full border-gray-300 rounded-md"
                                        required>
                                    <option value="">Select Customer</option>

                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}"
                                            {{ old('customer_id', $sale->customer_id) == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Invoice Number
                                </label>

                                <input type="text"
                                       name="invoice_number"
                                       value="{{ old('invoice_number', $sale->invoice_number) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md"
                                       required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Sale Date
                                </label>

                                <input type="date"
                                       name="sale_date"
                                       value="{{ old('sale_date', $sale->sale_date->format('Y-m-d')) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md"
                                       required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Status
                                </label>

                                <select name="status"
                                        class="mt-1 block w-full border-gray-300 rounded-md"
                                        required>

                                    <option value="pending"
                                        {{ old('status', $sale->status) === 'pending' ? 'selected' : '' }}>
                                        Pending
                                    </option>

                                    <option value="completed"
                                        {{ old('status', $sale->status) === 'completed' ? 'selected' : '' }}>
                                        Completed
                                    </option>

                                    <option value="cancelled"
                                        {{ old('status', $sale->status) === 'cancelled' ? 'selected' : '' }}>
                                        Cancelled
                                    </option>

                                </select>
                            </div>

                        </div>

                        <div class="mb-4">

                            <div class="flex justify-between items-center mb-3">
                                <h3 class="text-lg font-semibold">
                                    Sale Items
                                </h3>

                                <button type="button"
                                        id="add-item"
                                        class="px-4 py-2 bg-green-600 text-white rounded">
                                    + Add Item
                                </button>
                            </div>

                            <div class="overflow-x-auto">

                                <table class="min-w-full border border-gray-300">

                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="border px-3 py-2 text-left">
                                                Product
                                            </th>

                                            <th class="border px-3 py-2">
                                                Quantity
                                            </th>

                                            <th class="border px-3 py-2">
                                                Unit Price
                                            </th>

                                            <th class="border px-3 py-2">
                                                Total
                                            </th>

                                            <th class="border px-3 py-2">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody id="items-body">

                                        @foreach ($sale->items as $index => $item)

                                            <tr class="item-row">

                                                <td class="border px-3 py-2">

                                                    <select name="items[{{ $index }}][product_id]"
                                                            class="product-select w-full border-gray-300 rounded"
                                                            required>

                                                        <option value="">
                                                            Select Product
                                                        </option>

                                                        @foreach ($products as $product)

                                                            <option value="{{ $product->id }}"
                                                                {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                                {{ $product->name }}
                                                            </option>

                                                        @endforeach

                                                    </select>

                                                </td>

                                                <td class="border px-3 py-2">

                                                    <input type="number"
                                                           name="items[{{ $index }}][quantity]"
                                                           class="quantity w-full border-gray-300 rounded"
                                                           value="{{ old("items.$index.quantity", $item->quantity) }}"
                                                           min="0.01"
                                                           step="0.01"
                                                           required>

                                                </td>

                                                <td class="border px-3 py-2">

                                                    <input type="number"
                                                           name="items[{{ $index }}][unit_price]"
                                                           class="unit-price w-full border-gray-300 rounded"
                                                           value="{{ old("items.$index.unit_price", $item->unit_price) }}"
                                                           min="0"
                                                           step="0.01"
                                                           required>

                                                </td>

                                                <td class="border px-3 py-2 text-right">

                                                    <span class="item-total">
                                                        {{ number_format($item->quantity * $item->unit_price, 2, '.', '') }}
                                                    </span>

                                                </td>

                                                <td class="border px-3 py-2 text-center">

                                                    <button type="button"
                                                            class="remove-item px-3 py-1 bg-red-600 text-white rounded">
                                                        Delete
                                                    </button>

                                                </td>

                                            </tr>

                                        @endforeach

                                    </tbody>

                                    <tfoot>

                                        <tr>

                                            <td colspan="3"
                                                class="border px-3 py-3 text-right font-bold">
                                                Grand Total
                                            </td>

                                            <td class="border px-3 py-3 text-right font-bold">
                                                Rs.
                                                <span id="grand-total">
                                                    {{ number_format($sale->total_amount, 2, '.', '') }}
                                                </span>
                                            </td>

                                            <td class="border"></td>

                                        </tr>

                                    </tfoot>

                                </table>

                            </div>

                        </div>

                        <div class="mb-6">

                            <label class="block text-sm font-medium text-gray-700">
                                Notes
                            </label>

                            <textarea name="notes"
                                      rows="4"
                                      class="mt-1 block w-full border-gray-300 rounded-md">{{ old('notes', $sale->notes) }}</textarea>

                        </div>

                        <div class="flex gap-2">

                            <a href="{{ route('sales.show', $sale) }}"
                               class="px-4 py-2 bg-gray-200 text-gray-800 rounded">
                                Cancel
                            </a>

                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded">
                                Update Sale
                            </button>

                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

    <script>

        let itemIndex = {{ $sale->items->count() }};

        const productsHtml = `
            <option value="">Select Product</option>

            @foreach ($products as $product)
                <option value="{{ $product->id }}">
                    {{ $product->name }}
                </option>
            @endforeach
        `;

        function calculateRow(row) {

            const quantity =
                parseFloat(row.querySelector('.quantity').value) || 0;

            const unitPrice =
                parseFloat(row.querySelector('.unit-price').value) || 0;

            const total = quantity * unitPrice;

            row.querySelector('.item-total').textContent =
                total.toFixed(2);

            calculateGrandTotal();
        }

        function calculateGrandTotal() {

            let grandTotal = 0;

            document.querySelectorAll('.item-row').forEach(row => {

                const quantity =
                    parseFloat(row.querySelector('.quantity').value) || 0;

                const unitPrice =
                    parseFloat(row.querySelector('.unit-price').value) || 0;

                grandTotal += quantity * unitPrice;

            });

            document.getElementById('grand-total').textContent =
                grandTotal.toFixed(2);
        }

        function attachRowEvents(row) {

            row.querySelector('.quantity')
                .addEventListener('input', () => {
                    calculateRow(row);
                });

            row.querySelector('.unit-price')
                .addEventListener('input', () => {
                    calculateRow(row);
                });

            row.querySelector('.remove-item')
                .addEventListener('click', () => {

                    const rows =
                        document.querySelectorAll('.item-row');

                    if (rows.length > 1) {

                        row.remove();

                        calculateGrandTotal();

                    }

                });
        }

        document.querySelectorAll('.item-row').forEach(row => {
            attachRowEvents(row);
        });

        document.getElementById('add-item')
            .addEventListener('click', () => {

                const row =
                    document.createElement('tr');

                row.className = 'item-row';

                row.innerHTML = `

                    <td class="border px-3 py-2">

                        <select name="items[${itemIndex}][product_id]"
                                class="product-select w-full border-gray-300 rounded"
                                required>

                            ${productsHtml}

                        </select>

                    </td>

                    <td class="border px-3 py-2">

                        <input type="number"
                               name="items[${itemIndex}][quantity]"
                               class="quantity w-full border-gray-300 rounded"
                               value="1"
                               min="0.01"
                               step="0.01"
                               required>

                    </td>

                    <td class="border px-3 py-2">

                        <input type="number"
                               name="items[${itemIndex}][unit_price]"
                               class="unit-price w-full border-gray-300 rounded"
                               value="0"
                               min="0"
                               step="0.01"
                               required>

                    </td>

                    <td class="border px-3 py-2 text-right">

                        <span class="item-total">
                            0.00
                        </span>

                    </td>

                    <td class="border px-3 py-2 text-center">

                        <button type="button"
                                class="remove-item px-3 py-1 bg-red-600 text-white rounded">
                            Delete
                        </button>

                    </td>

                `;

                document
                    .getElementById('items-body')
                    .appendChild(row);

                attachRowEvents(row);

                itemIndex++;

            });

        calculateGrandTotal();

    </script>

</x-app-layout>
```
