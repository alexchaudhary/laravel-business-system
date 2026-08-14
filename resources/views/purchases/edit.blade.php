```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Purchase
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Validation Errors --}}
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST"
                          action="{{ route('purchases.update', $purchase) }}"
                          id="purchaseForm">

                        @csrf
                        @method('PUT')

                        {{-- Supplier --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">
                                Supplier
                            </label>

                            <select name="supplier_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    required>

                                <option value="">Select Supplier</option>

                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ old('supplier_id', $purchase->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        {{-- Invoice Number --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">
                                Invoice Number
                            </label>

                            <input type="text"
                                   name="invoice_number"
                                   value="{{ old('invoice_number', $purchase->invoice_number) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                   required>
                        </div>

                        {{-- Purchase Date --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">
                                Purchase Date
                            </label>

                            <input type="date"
                                   name="purchase_date"
                                   value="{{ old('purchase_date', $purchase->purchase_date->format('Y-m-d')) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                   required>
                        </div>

                        {{-- Status --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700">
                                Status
                            </label>

                            <select name="status"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    required>

                                <option value="pending"
                                    {{ old('status', $purchase->status) === 'pending' ? 'selected' : '' }}>
                                    Pending
                                </option>

                                <option value="received"
                                    {{ old('status', $purchase->status) === 'received' ? 'selected' : '' }}>
                                    Received
                                </option>

                                <option value="cancelled"
                                    {{ old('status', $purchase->status) === 'cancelled' ? 'selected' : '' }}>
                                    Cancelled
                                </option>

                            </select>
                        </div>


                        {{-- Purchase Items --}}
                        <div class="mb-6">

                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold">
                                    Purchase Items
                                </h3>

                                <button type="button"
                                        id="addItem"
                                        class="px-4 py-2 bg-green-600 text-white rounded">
                                    + Add Item
                                </button>
                            </div>

                            <div class="overflow-x-auto">

                                <table class="w-full border-collapse">

                                    <thead>
                                        <tr class="border-b">
                                            <th class="text-left p-2">
                                                Product
                                            </th>

                                            <th class="text-left p-2">
                                                Quantity
                                            </th>

                                            <th class="text-left p-2">
                                                Unit Price
                                            </th>

                                            <th class="text-left p-2">
                                                Total
                                            </th>

                                            <th class="text-left p-2">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody id="itemsContainer">

                                        @forelse($purchase->items as $index => $item)

                                            <tr class="item-row border-b">

                                                {{-- Product --}}
                                                <td class="p-2">
                                                    <select name="items[{{ $index }}][product_id]"
                                                            class="product-select w-full border-gray-300 rounded-md"
                                                            required>

                                                        <option value="">
                                                            Select Product
                                                        </option>

                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}"
                                                                {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                                {{ $product->name }}
                                                            </option>
                                                        @endforeach

                                                    </select>
                                                </td>

                                                {{-- Quantity --}}
                                                <td class="p-2">
                                                    <input type="number"
                                                           name="items[{{ $index }}][quantity]"
                                                           value="{{ $item->quantity }}"
                                                           class="quantity w-full border-gray-300 rounded-md"
                                                           step="0.01"
                                                           min="0.01"
                                                           required>
                                                </td>

                                                {{-- Unit Price --}}
                                                <td class="p-2">
                                                    <input type="number"
                                                           name="items[{{ $index }}][unit_price]"
                                                           value="{{ $item->unit_price }}"
                                                           class="unit-price w-full border-gray-300 rounded-md"
                                                           step="0.01"
                                                           min="0"
                                                           required>
                                                </td>

                                                {{-- Item Total --}}
                                                <td class="p-2">
                                                    <input type="number"
                                                           class="item-total w-full border-gray-300 rounded-md bg-gray-100"
                                                           value="{{ $item->total }}"
                                                           step="0.01"
                                                           readonly>
                                                </td>

                                                {{-- Remove --}}
                                                <td class="p-2">
                                                    <button type="button"
                                                            class="remove-item px-3 py-2 bg-red-600 text-white rounded">
                                                        Remove
                                                    </button>
                                                </td>

                                            </tr>

                                        @empty

                                            {{-- Fallback row --}}
                                            <tr class="item-row border-b">

                                                <td class="p-2">
                                                    <select name="items[0][product_id]"
                                                            class="product-select w-full border-gray-300 rounded-md"
                                                            required>

                                                        <option value="">
                                                            Select Product
                                                        </option>

                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}">
                                                                {{ $product->name }}
                                                            </option>
                                                        @endforeach

                                                    </select>
                                                </td>

                                                <td class="p-2">
                                                    <input type="number"
                                                           name="items[0][quantity]"
                                                           value="1"
                                                           class="quantity w-full border-gray-300 rounded-md"
                                                           step="0.01"
                                                           min="0.01"
                                                           required>
                                                </td>

                                                <td class="p-2">
                                                    <input type="number"
                                                           name="items[0][unit_price]"
                                                           value="0"
                                                           class="unit-price w-full border-gray-300 rounded-md"
                                                           step="0.01"
                                                           min="0"
                                                           required>
                                                </td>

                                                <td class="p-2">
                                                    <input type="number"
                                                           class="item-total w-full border-gray-300 rounded-md bg-gray-100"
                                                           value="0"
                                                           step="0.01"
                                                           readonly>
                                                </td>

                                                <td class="p-2">
                                                    <button type="button"
                                                            class="remove-item px-3 py-2 bg-red-600 text-white rounded">
                                                        Remove
                                                    </button>
                                                </td>

                                            </tr>

                                        @endforelse

                                    </tbody>

                                </table>

                            </div>

                        </div>


                        {{-- Total Amount --}}
                        <div class="mb-6">

                            <label class="block text-sm font-medium text-gray-700">
                                Total Amount
                            </label>

                            <input type="number"
                                   id="totalAmount"
                                   name="total_amount"
                                   value="{{ number_format($purchase->total_amount, 2, '.', '') }}"
                                   step="0.01"
                                   min="0"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100"
                                   readonly>

                        </div>


                        {{-- Notes --}}
                        <div class="mb-6">

                            <label class="block text-sm font-medium text-gray-700">
                                Notes
                            </label>

                            <textarea name="notes"
                                      rows="4"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('notes', $purchase->notes) }}</textarea>

                        </div>


                        {{-- Buttons --}}
                        <div class="flex gap-2">

                            <a href="{{ route('purchases.show', $purchase) }}"
                               class="px-4 py-2 bg-gray-200 text-gray-800 rounded">
                                Cancel
                            </a>

                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded">
                                Update Purchase
                            </button>

                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>


    {{-- JavaScript --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const itemsContainer = document.getElementById('itemsContainer');
            const addItemButton = document.getElementById('addItem');
            const totalAmount = document.getElementById('totalAmount');

            let itemIndex = itemsContainer.querySelectorAll('.item-row').length;


            // Calculate one row total
            function calculateRow(row) {

                const quantityInput = row.querySelector('.quantity');
                const unitPriceInput = row.querySelector('.unit-price');
                const itemTotalInput = row.querySelector('.item-total');

                const quantity = parseFloat(quantityInput.value) || 0;
                const unitPrice = parseFloat(unitPriceInput.value) || 0;

                const total = quantity * unitPrice;

                itemTotalInput.value = total.toFixed(2);

                return total;
            }


            // Calculate complete purchase total
            function calculateGrandTotal() {

                let grandTotal = 0;

                const rows = itemsContainer.querySelectorAll('.item-row');

                rows.forEach(function (row) {
                    grandTotal += calculateRow(row);
                });

                totalAmount.value = grandTotal.toFixed(2);
            }


            // Add new item
            addItemButton.addEventListener('click', function () {

                const row = document.createElement('tr');

                row.className = 'item-row border-b';

                row.innerHTML = `
                    <td class="p-2">
                        <select name="items[${itemIndex}][product_id]"
                                class="product-select w-full border-gray-300 rounded-md"
                                required>

                            <option value="">
                                Select Product
                            </option>

                            @foreach($products as $product)
                                <option value="{{ $product->id }}">
                                    {{ $product->name }}
                                </option>
                            @endforeach

                        </select>
                    </td>

                    <td class="p-2">
                        <input type="number"
                               name="items[${itemIndex}][quantity]"
                               value="1"
                               class="quantity w-full border-gray-300 rounded-md"
                               step="0.01"
                               min="0.01"
                               required>
                    </td>

                    <td class="p-2">
                        <input type="number"
                               name="items[${itemIndex}][unit_price]"
                               value="0"
                               class="unit-price w-full border-gray-300 rounded-md"
                               step="0.01"
                               min="0"
                               required>
                    </td>

                    <td class="p-2">
                        <input type="number"
                               class="item-total w-full border-gray-300 rounded-md bg-gray-100"
                               value="0.00"
                               step="0.01"
                               readonly>
                    </td>

                    <td class="p-2">
                        <button type="button"
                                class="remove-item px-3 py-2 bg-red-600 text-white rounded">
                            Remove
                        </button>
                    </td>
                `;

                itemsContainer.appendChild(row);

                itemIndex++;

                calculateGrandTotal();
            });


            // Quantity / Unit Price change
            itemsContainer.addEventListener('input', function (event) {

                if (
                    event.target.classList.contains('quantity') ||
                    event.target.classList.contains('unit-price')
                ) {
                    calculateGrandTotal();
                }

            });


            // Remove item
            itemsContainer.addEventListener('click', function (event) {

                if (event.target.classList.contains('remove-item')) {

                    const rows = itemsContainer.querySelectorAll('.item-row');

                    // At least one item must remain
                    if (rows.length <= 1) {
                        alert('At least one purchase item is required.');
                        return;
                    }

                    event.target.closest('.item-row').remove();

                    calculateGrandTotal();
                }

            });


            // Initial calculation
            calculateGrandTotal();

        });
    </script>

</x-app-layout>
```
