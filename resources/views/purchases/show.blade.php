<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Purchase Details
        </h2>
    </x-slot>

```
<div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">

                {{-- Header --}}
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold">
                        Purchase #{{ $purchase->id }}
                    </h3>

                    <div class="flex gap-2">
                        <a href="{{ route('purchases.edit', $purchase) }}"
                           class="px-4 py-2 bg-blue-600 text-white rounded">
                            Edit
                        </a>

                        <a href="{{ route('purchases.index') }}"
                           class="px-4 py-2 bg-gray-200 text-gray-800 rounded">
                            Back
                        </a>
                    </div>
                </div>

                {{-- Purchase Information --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">

                    <div>
                        <strong>Invoice Number:</strong>
                        {{ $purchase->invoice_number }}
                    </div>

                    <div>
                        <strong>Supplier:</strong>
                        {{ $purchase->supplier->name }}
                    </div>

                    <div>
                        <strong>Purchase Date:</strong>
                        {{ $purchase->purchase_date->format('Y-m-d') }}
                    </div>

                    <div>
                        <strong>Status:</strong>
                        {{ ucfirst($purchase->status) }}
                    </div>

                    <div>
                        <strong>Total Amount:</strong>
                        Rs. {{ number_format($purchase->total_amount, 2) }}
                    </div>

                    <div>
                        <strong>Notes:</strong>
                        {{ $purchase->notes ?: 'No notes available.' }}
                    </div>

                </div>

                {{-- Purchase Items --}}
                <div class="mb-4">
                    <h3 class="text-lg font-semibold mb-4">
                        Purchase Items
                    </h3>

                    @if ($purchase->items->count() > 0)

                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse">

                                <thead>
                                    <tr class="border-b bg-gray-50">
                                        <th class="text-left p-3">
                                            Product
                                        </th>

                                        <th class="text-right p-3">
                                            Quantity
                                        </th>

                                        <th class="text-right p-3">
                                            Unit Price
                                        </th>

                                        <th class="text-right p-3">
                                            Total
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($purchase->items as $item)

                                        <tr class="border-b">

                                            <td class="p-3">
                                                {{ $item->product->name }}
                                            </td>

                                            <td class="p-3 text-right">
                                                {{ $item->quantity }}
                                            </td>

                                            <td class="p-3 text-right">
                                                Rs. {{ number_format($item->unit_price, 2) }}
                                            </td>

                                            <td class="p-3 text-right">
                                                Rs. {{ number_format($item->total, 2) }}
                                            </td>

                                        </tr>

                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr class="font-semibold">
                                        <td colspan="3" class="p-3 text-right">
                                            Total Amount:
                                        </td>

                                        <td class="p-3 text-right">
                                            Rs. {{ number_format($purchase->total_amount, 2) }}
                                        </td>
                                    </tr>
                                </tfoot>

                            </table>
                        </div>

                    @else

                        <div class="p-4 bg-gray-50 rounded">
                            No purchase items available.
                        </div>

                    @endif
                </div>

            </div>
        </div>

    </div>
</div>
```

</x-app-layout>
