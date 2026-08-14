<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Sales
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">
                            Sales Management
                        </h3>

                        <a href="{{ route('sales.create') }}"
                           class="px-4 py-2 bg-gray-800 text-white rounded">
                            + Add Sale
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-3">Invoice</th>
                                    <th class="text-left py-3">Customer</th>
                                    <th class="text-left py-3">Date</th>
                                    <th class="text-left py-3">Amount</th>
                                    <th class="text-left py-3">Status</th>
                                    <th class="text-right py-3">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($sales as $sale)
                                    <tr class="border-b">
                                        <td class="py-3">
                                            {{ $sale->invoice_number }}
                                        </td>

                                        <td class="py-3">
                                            {{ $sale->customer->name ?? 'N/A' }}
                                        </td>

                                        <td class="py-3">
                                            {{ $sale->sale_date->format('Y-m-d') }}
                                        </td>

                                        <td class="py-3">
                                            Rs. {{ number_format($sale->total_amount, 2) }}
                                        </td>

                                        <td class="py-3">
                                            {{ $sale->status }}
                                        </td>

                                        <td class="py-3 text-right">
                                            <a href="{{ route('sales.show', $sale) }}"
                                               class="text-blue-600 hover:underline">
                                                View
                                            </a>

                                            <a href="{{ route('sales.edit', $sale) }}"
                                               class="ml-2 text-green-600 hover:underline">
                                                Edit
                                            </a>

                                            <form action="{{ route('sales.destroy', $sale) }}"
                                                  method="POST"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="ml-2 text-red-600 hover:underline"
                                                        onclick="return confirm('Are you sure you want to delete this sale?')">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-6 text-center text-gray-500">
                                            No sales found.
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