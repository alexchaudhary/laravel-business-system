<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Sale Details
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">
                            Sale Details
                        </h3>

                        <div class="flex gap-2">
                            <a href="{{ route('sales.edit', $sale) }}"
                               class="px-4 py-2 bg-blue-600 text-white rounded">
                                Edit
                            </a>

                            <a href="{{ route('sales.index') }}"
                               class="px-4 py-2 bg-gray-200 text-gray-800 rounded">
                                Back
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

                        <div>
                            <p class="text-sm text-gray-500">Invoice Number</p>
                            <p class="font-semibold">
                                {{ $sale->invoice_number }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Customer</p>
                            <p class="font-semibold">
                                {{ $sale->customer->name }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Sale Date</p>
                            <p class="font-semibold">
                                {{ $sale->sale_date }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            <p class="font-semibold">
                                {{ ucfirst($sale->status) }}
                            </p>
                        </div>

                    </div>

                    <h3 class="text-lg font-semibold mb-3">
                        Sale Items
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-300">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border px-3 py-2 text-left">
                                        Product
                                    </th>
                                    <th class="border px-3 py-2 text-right">
                                        Quantity
                                    </th>
                                    <th class="border px-3 py-2 text-right">
                                        Unit Price
                                    </th>
                                    <th class="border px-3 py-2 text-right">
                                        Total
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($sale->items as $item)
                                    <tr>
                                        <td class="border px-3 py-2">
                                            {{ $item->product->name }}
                                        </td>

                                        <td class="border px-3 py-2 text-right">
                                            {{ $item->quantity }}
                                        </td>

                                        <td class="border px-3 py-2 text-right">
                                            Rs. {{ number_format($item->unit_price, 2) }}
                                        </td>

                                        <td class="border px-3 py-2 text-right">
                                            Rs. {{ number_format($item->total, 2) }}
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
                                        Rs. {{ number_format($sale->total_amount, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if ($sale->notes)
                        <div class="mt-6">
                            <p class="text-sm text-gray-500">Notes</p>
                            <p class="mt-1">
                                {{ $sale->notes }}
                            </p>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>