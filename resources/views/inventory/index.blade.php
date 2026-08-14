<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Inventory
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

                {{-- Total Products --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">
                        Total Products
                    </div>

                    <div class="text-3xl font-bold text-gray-800 mt-2">
                        {{ $totalProducts }}
                    </div>
                </div>

                {{-- Total Stock Units --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">
                        Total Stock Units
                    </div>

                    <div class="text-3xl font-bold text-gray-800 mt-2">
                        {{ number_format((float) $totalStockUnits, 2) }}
                    </div>
                </div>

                {{-- Stock Value --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">
                        Total Stock Value
                    </div>

                    <div class="text-2xl font-bold text-blue-600 mt-2">
                        Rs. {{ number_format((float) $totalStockValue, 2) }}
                    </div>
                </div>

                {{-- Low Stock --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">
                        Low Stock Products
                    </div>

                    <div class="text-3xl font-bold text-red-600 mt-2">
                        {{ $lowStockProducts->where('stock_quantity', '>', 0)->count() }}
                    </div>
                </div>

            </div>


            {{-- Inventory Table --}}
            <div class="bg-white shadow-sm sm:rounded-lg">

                <div class="p-6 text-gray-900">

                    {{-- Header --}}
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-5">

                        <div>
                            <h3 class="text-lg font-semibold">
                                Stock Overview
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Current stock level and value of all products.
                            </p>
                        </div>

                        <a href="{{ route('products.index') }}"
                           class="inline-flex justify-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Manage Products
                        </a>

                    </div>


                    {{-- Search & Filter --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">

                        {{-- Search --}}
                        <div class="md:col-span-2">

                            <label for="inventorySearch"
                                   class="block text-sm font-medium text-gray-700 mb-1">
                                Search Product
                            </label>

                            <input
                                type="text"
                                id="inventorySearch"
                                placeholder="Search by product name or SKU..."
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >

                        </div>


                        {{-- Status Filter --}}
                        <div>

                            <label for="statusFilter"
                                   class="block text-sm font-medium text-gray-700 mb-1">
                                Filter by Status
                            </label>

                            <select
                                id="statusFilter"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="all">All Status</option>
                                <option value="in-stock">In Stock</option>
                                <option value="low-stock">Low Stock</option>
                                <option value="out-of-stock">Out of Stock</option>
                            </select>

                        </div>

                    </div>


                    {{-- Table --}}
                    <div class="overflow-x-auto">

                        <table class="min-w-full border border-gray-300">

                            <thead class="bg-gray-100">

                                <tr>

                                    <th class="border px-4 py-3 text-left">
                                        Product
                                    </th>

                                    <th class="border px-4 py-3 text-left">
                                        SKU
                                    </th>

                                    <th class="border px-4 py-3 text-right">
                                        Purchase Price
                                    </th>

                                    <th class="border px-4 py-3 text-right">
                                        Selling Price
                                    </th>

                                    <th class="border px-4 py-3 text-right">
                                        Stock
                                    </th>

                                    <th class="border px-4 py-3 text-right">
                                        Stock Value
                                    </th>

                                    <th class="border px-4 py-3 text-center">
                                        Status
                                    </th>

                                </tr>

                            </thead>


                            <tbody id="inventoryTableBody">

                                @forelse ($products as $product)

                                    @php
                                        $stock = (float) $product->stock_quantity;

                                        if ($stock <= 0) {
                                            $status = 'out-of-stock';
                                            $statusText = 'Out of Stock';
                                        } elseif ($stock <= 5) {
                                            $status = 'low-stock';
                                            $statusText = 'Low Stock';
                                        } else {
                                            $status = 'in-stock';
                                            $statusText = 'In Stock';
                                        }

                                        $stockValue =
                                            $stock * (float) $product->purchase_price;
                                    @endphp


                                    <tr
                                        class="inventory-row hover:bg-gray-50"
                                        data-product="{{ strtolower($product->name) }}"
                                        data-sku="{{ strtolower($product->sku ?? '') }}"
                                        data-status="{{ $status }}"
                                    >

                                        {{-- Product --}}
                                        <td class="border px-4 py-3 font-medium">
                                            {{ $product->name }}
                                        </td>


                                        {{-- SKU --}}
                                        <td class="border px-4 py-3">
                                            {{ $product->sku ?? '-' }}
                                        </td>


                                        {{-- Purchase Price --}}
                                        <td class="border px-4 py-3 text-right">
                                            Rs.
                                            {{ number_format((float) $product->purchase_price, 2) }}
                                        </td>


                                        {{-- Selling Price --}}
                                        <td class="border px-4 py-3 text-right">
                                            Rs.
                                            {{ number_format((float) $product->selling_price, 2) }}
                                        </td>


                                        {{-- Stock --}}
                                        <td class="border px-4 py-3 text-right font-bold">
                                            {{ number_format($stock, 2) }}
                                        </td>


                                        {{-- Stock Value --}}
                                        <td class="border px-4 py-3 text-right font-semibold">
                                            Rs.
                                            {{ number_format($stockValue, 2) }}
                                        </td>


                                        {{-- Status --}}
                                        <td class="border px-4 py-3 text-center">

                                            @if ($stock <= 0)

                                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                                    Out of Stock
                                                </span>

                                            @elseif ($stock <= 5)

                                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">
                                                    Low Stock
                                                </span>

                                            @else

                                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                                    In Stock
                                                </span>

                                            @endif

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="7"
                                            class="border px-4 py-8 text-center text-gray-500">

                                            No products found.

                                        </td>

                                    </tr>

                                @endforelse


                                {{-- No Search Result --}}
                                <tr id="noSearchResult" class="hidden">

                                    <td colspan="7"
                                        class="border px-4 py-8 text-center text-gray-500">

                                        No matching products found.

                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>
    </div>


    {{-- Search & Filter Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const searchInput = document.getElementById('inventorySearch');
            const statusFilter = document.getElementById('statusFilter');
            const rows = document.querySelectorAll('.inventory-row');
            const noSearchResult = document.getElementById('noSearchResult');

            function filterInventory() {

                const searchValue = searchInput.value.toLowerCase().trim();
                const selectedStatus = statusFilter.value;

                let visibleRows = 0;

                rows.forEach(function (row) {

                    const productName = row.dataset.product;
                    const sku = row.dataset.sku;
                    const status = row.dataset.status;

                    const matchesSearch =
                        productName.includes(searchValue) ||
                        sku.includes(searchValue);

                    const matchesStatus =
                        selectedStatus === 'all' ||
                        status === selectedStatus;

                    if (matchesSearch && matchesStatus) {

                        row.style.display = '';

                        visibleRows++;

                    } else {

                        row.style.display = 'none';

                    }

                });


                if (visibleRows === 0 && rows.length > 0) {

                    noSearchResult.classList.remove('hidden');

                } else {

                    noSearchResult.classList.add('hidden');

                }

            }


            searchInput.addEventListener('input', filterInventory);

            statusFilter.addEventListener('change', filterInventory);

        });
    </script>

</x-app-layout>

