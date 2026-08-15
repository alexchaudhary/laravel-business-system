<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    Dashboard
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Business overview and current performance
                </p>
            </div>

            <div class="text-sm text-gray-500">
                {{ now()->format('d M Y') }}
            </div>
        </div>
    </x-slot>


    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">


            {{-- =========================================================
                SUMMARY CARDS
            ========================================================== --}}

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">


                {{-- Customers --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6
                            hover:shadow-md transition">

                    <div class="flex items-start justify-between">

                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                Total Customers
                            </p>

                            <p class="text-3xl font-bold text-gray-800 mt-2">
                                {{ number_format($totalCustomers) }}
                            </p>
                        </div>

                        <div class="bg-blue-50 text-blue-600 rounded-lg p-3">
                            👥
                        </div>

                    </div>

                    <a href="{{ route('customers.index') }}"
                       class="inline-block text-sm font-medium text-blue-600 hover:text-blue-800 mt-4">
                        View Customers →
                    </a>
                </div>


                {{-- Suppliers --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6
                            hover:shadow-md transition">

                    <div class="flex items-start justify-between">

                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                Total Suppliers
                            </p>

                            <p class="text-3xl font-bold text-gray-800 mt-2">
                                {{ number_format($totalSuppliers) }}
                            </p>
                        </div>

                        <div class="bg-purple-50 text-purple-600 rounded-lg p-3">
                            🏢
                        </div>

                    </div>

                    <a href="{{ route('suppliers.index') }}"
                       class="inline-block text-sm font-medium text-purple-600 hover:text-purple-800 mt-4">
                        View Suppliers →
                    </a>
                </div>


                {{-- Products --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6
                            hover:shadow-md transition">

                    <div class="flex items-start justify-between">

                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                Total Products
                            </p>

                            <p class="text-3xl font-bold text-gray-800 mt-2">
                                {{ number_format($totalProducts) }}
                            </p>
                        </div>

                        <div class="bg-orange-50 text-orange-600 rounded-lg p-3">
                            📦
                        </div>

                    </div>

                    <a href="{{ route('products.index') }}"
                       class="inline-block text-sm font-medium text-orange-600 hover:text-orange-800 mt-4">
                        View Products →
                    </a>
                </div>


                {{-- Stock Units --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6
                            hover:shadow-md transition">

                    <div class="flex items-start justify-between">

                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                Stock Units
                            </p>

                            <p class="text-3xl font-bold text-gray-800 mt-2">
                                {{ number_format((float) $totalStockUnits, 2) }}
                            </p>

                            <p class="text-sm text-gray-500 mt-2">
                                Stock Value:
                                <span class="font-semibold text-gray-700">
                                    Rs. {{ number_format((float) $totalStockValue, 2) }}
                                </span>
                            </p>
                        </div>

                        <div class="bg-green-50 text-green-600 rounded-lg p-3">
                            📊
                        </div>

                    </div>

                    <a href="{{ route('inventory.index') }}"
                       class="inline-block text-sm font-medium text-green-600 hover:text-green-800 mt-4">
                        View Inventory →
                    </a>
                </div>


                {{-- Sales --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6
                            hover:shadow-md transition">

                    <div class="flex items-start justify-between">

                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                Total Sales
                            </p>

                            <p class="text-3xl font-bold text-gray-800 mt-2">
                                {{ number_format($totalSales) }}
                            </p>
                        </div>

                        <div class="bg-yellow-50 text-yellow-600 rounded-lg p-3">
                            💰
                        </div>

                    </div>

                    <a href="{{ route('sales.index') }}"
                       class="inline-block text-sm font-medium text-yellow-600 hover:text-yellow-800 mt-4">
                        View Sales →
                    </a>
                </div>


                {{-- Purchases --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6
                            hover:shadow-md transition">

                    <div class="flex items-start justify-between">

                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                Total Purchases
                            </p>

                            <p class="text-3xl font-bold text-gray-800 mt-2">
                                {{ number_format($totalPurchases) }}
                            </p>
                        </div>

                        <div class="bg-red-50 text-red-600 rounded-lg p-3">
                            🛒
                        </div>

                    </div>

                    <a href="{{ route('purchases.index') }}"
                       class="inline-block text-sm font-medium text-red-600 hover:text-red-800 mt-4">
                        View Purchases →
                    </a>
                </div>

            </div>



            {{-- =========================================================
                LOW STOCK PRODUCTS
            ========================================================== --}}

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm mb-8">

                <div class="p-6 border-b border-gray-200">

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                Low Stock Products
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Products that need attention
                            </p>
                        </div>

                        <a href="{{ route('inventory.index') }}"
                           class="text-sm font-medium text-blue-600 hover:text-blue-800">
                            View Inventory →
                        </a>

                    </div>

                </div>


                @if ($lowStockProducts->count())

                    <div class="overflow-x-auto">

                        <table class="min-w-full">

                            <thead class="bg-gray-50">
                                <tr>

                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                        Product
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                        SKU
                                    </th>

                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                                        Stock
                                    </th>

                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                                        Threshold
                                    </th>

                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">
                                        Status
                                    </th>

                                </tr>
                            </thead>


                            <tbody class="divide-y divide-gray-200">

                                @foreach ($lowStockProducts as $product)

                                    <tr class="hover:bg-gray-50">

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-medium text-gray-800">
                                                {{ $product->name }}
                                            </div>
                                        </td>


                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $product->sku ?? '-' }}
                                        </td>


                                        <td class="px-6 py-4 whitespace-nowrap text-right font-semibold text-gray-800">
                                            {{ number_format((float) $product->stock_quantity, 2) }}
                                        </td>


                                        <td class="px-6 py-4 whitespace-nowrap text-right text-gray-600">
                                            {{ number_format((float) $product->low_stock_threshold, 2) }}
                                        </td>


                                        <td class="px-6 py-4 whitespace-nowrap text-center">

                                            <span class="inline-flex items-center px-3 py-1 rounded-full
                                                         text-xs font-semibold bg-yellow-100 text-yellow-700">

                                                Low Stock

                                            </span>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <div class="p-6">

                        <div class="rounded-lg bg-green-50 border border-green-200 p-4">

                            <div class="flex items-center gap-2">

                                <span class="text-green-600">
                                    ✓
                                </span>

                                <p class="text-sm font-medium text-green-700">
                                    No low stock products right now.
                                </p>

                            </div>

                        </div>

                    </div>

                @endif

            </div>



            {{-- =========================================================
                RECENT SALES & PURCHASES
            ========================================================== --}}

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">


                {{-- Recent Sales --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

                    <div class="p-6 border-b border-gray-200">

                        <div class="flex items-center justify-between">

                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">
                                    Recent Sales
                                </h3>

                                <p class="text-sm text-gray-500 mt-1">
                                    Latest sales transactions
                                </p>
                            </div>

                            <a href="{{ route('sales.index') }}"
                               class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                View All →
                            </a>

                        </div>

                    </div>


                    <div class="px-6">

                        @forelse ($recentSales as $sale)

                            <div class="flex items-center justify-between py-4 border-b last:border-b-0">

                                <div class="min-w-0">

                                    <p class="font-medium text-gray-800 truncate">
                                        {{ $sale->invoice_number }}
                                    </p>

                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ $sale->customer?->name ?? 'Walk-in Customer' }}
                                    </p>

                                </div>


                                <div class="text-right ml-4">

                                    <p class="font-semibold text-gray-800 whitespace-nowrap">
                                        Rs. {{ number_format((float) $sale->total_amount, 2) }}
                                    </p>

                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $sale->sale_date?->format('Y-m-d') }}
                                    </p>

                                </div>

                            </div>

                        @empty

                            <div class="py-6">

                                <p class="text-sm text-gray-500">
                                    No sales found.
                                </p>

                            </div>

                        @endforelse

                    </div>

                </div>



                {{-- Recent Purchases --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

                    <div class="p-6 border-b border-gray-200">

                        <div class="flex items-center justify-between">

                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">
                                    Recent Purchases
                                </h3>

                                <p class="text-sm text-gray-500 mt-1">
                                    Latest purchase transactions
                                </p>
                            </div>

                            <a href="{{ route('purchases.index') }}"
                               class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                View All →
                            </a>

                        </div>

                    </div>


                    <div class="px-6">

                        @forelse ($recentPurchases as $purchase)

                            <div class="flex items-center justify-between py-4 border-b last:border-b-0">

                                <div class="min-w-0">

                                    <p class="font-medium text-gray-800 truncate">
                                        {{ $purchase->invoice_number ?? 'Purchase #' . $purchase->id }}
                                    </p>

                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ $purchase->supplier?->name ?? 'Unknown Supplier' }}
                                    </p>

                                </div>


                                <div class="text-right ml-4">

                                    <p class="font-semibold text-gray-800 whitespace-nowrap">
                                        Rs. {{ number_format((float) $purchase->total_amount, 2) }}
                                    </p>

                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $purchase->purchase_date?->format('Y-m-d') }}
                                    </p>

                                </div>

                            </div>

                        @empty

                            <div class="py-6">

                                <p class="text-sm text-gray-500">
                                    No purchases found.
                                </p>

                            </div>

                        @endforelse

                    </div>

                </div>

            </div>

        </div>
    </div>

</x-app-layout>

