<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Stock Adjustments
            </h2>

            <a
                href="{{ route('stock-adjustments.create') }}"
                class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700"
            >
                Adjust Stock
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold">
                            Stock Adjustment History
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Track all stock increases and decreases.
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-300">

                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border px-4 py-3 text-left">
                                        Product
                                    </th>

                                    <th class="border px-4 py-3 text-center">
                                        Type
                                    </th>

                                    <th class="border px-4 py-3 text-right">
                                        Quantity
                                    </th>

                                    <th class="border px-4 py-3 text-right">
                                        Stock Before
                                    </th>

                                    <th class="border px-4 py-3 text-right">
                                        Stock After
                                    </th>

                                    <th class="border px-4 py-3 text-left">
                                        Note
                                    </th>

                                    <th class="border px-4 py-3 text-left">
                                        Date
                                    </th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse ($adjustments as $adjustment)

                                    <tr class="hover:bg-gray-50">

                                        <td class="border px-4 py-3 font-medium">
                                            {{ $adjustment->product->name ?? 'Deleted Product' }}
                                        </td>

                                        <td class="border px-4 py-3 text-center">

                                            @if ($adjustment->type === 'in')

                                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                                    Stock In
                                                </span>

                                            @else

                                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                                    Stock Out
                                                </span>

                                            @endif

                                        </td>

                                        <td class="border px-4 py-3 text-right font-semibold">
                                            {{ number_format((float) $adjustment->quantity, 2) }}
                                        </td>

                                        <td class="border px-4 py-3 text-right">
                                            {{ number_format((float) $adjustment->stock_before, 2) }}
                                        </td>

                                        <td class="border px-4 py-3 text-right font-semibold">
                                            {{ number_format((float) $adjustment->stock_after, 2) }}
                                        </td>

                                        <td class="border px-4 py-3">
                                            {{ $adjustment->note ?? '-' }}
                                        </td>

                                        <td class="border px-4 py-3 whitespace-nowrap">
                                            {{ $adjustment->created_at->format('Y-m-d H:i') }}
                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td
                                            colspan="7"
                                            class="border px-4 py-8 text-center text-gray-500"
                                        >
                                            No stock adjustments found.
                                        </td>
                                    </tr>

                                @endforelse

                            </tbody>

                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
