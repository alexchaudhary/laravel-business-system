<x-app-layout>
<x-slot name="header">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Stock Adjustment
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Adjust product stock quantity
            </p>
        </div>

        <a href="{{ route('inventory.index') }}"
           class="text-sm text-blue-600 hover:text-blue-800">
            ← Back to Inventory
        </a>
    </div>
</x-slot>

<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="p-6">

                {{-- Product Information --}}
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ $product->name }}
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        SKU: {{ $product->sku ?? '-' }}
                    </p>

                    <div class="mt-4 rounded-lg bg-gray-50 border p-4">
                        <p class="text-sm text-gray-500">
                            Current Stock
                        </p>

                        <p class="text-3xl font-bold text-gray-800 mt-1">
                            {{ number_format((float) $product->stock_quantity, 2) }}
                        </p>
                    </div>
                </div>

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="mb-6 rounded-md bg-red-50 border border-red-200 p-4">
                        <ul class="list-disc list-inside text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Adjustment Form --}}
                <form
                    method="POST"
                    action="{{ route('stock-adjustments.store', $product) }}"
                >
                    @csrf

                    {{-- Adjustment Type --}}
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Adjustment Type
                        </label>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            {{-- Increase --}}
                            <label class="flex items-center gap-3 border rounded-lg p-4 cursor-pointer hover:bg-green-50">
                                <input
                                    type="radio"
                                    name="type"
                                    value="increase"
                                    class="text-green-600"
                                    {{ old('type', 'increase') === 'increase' ? 'checked' : '' }}
                                    required
                                >

                                <div>
                                    <p class="font-semibold text-green-700">
                                        Increase
                                    </p>

                                    <p class="text-xs text-gray-500">
                                        Add stock
                                    </p>
                                </div>
                            </label>

                            {{-- Decrease --}}
                            <label class="flex items-center gap-3 border rounded-lg p-4 cursor-pointer hover:bg-red-50">
                                <input
                                    type="radio"
                                    name="type"
                                    value="decrease"
                                    class="text-red-600"
                                    {{ old('type') === 'decrease' ? 'checked' : '' }}
                                >

                                <div>
                                    <p class="font-semibold text-red-700">
                                        Decrease
                                    </p>

                                    <p class="text-xs text-gray-500">
                                        Remove stock
                                    </p>
                                </div>
                            </label>

                        </div>

                        @error('type')
                            <p class="text-sm text-red-600 mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Quantity --}}
                    <div class="mb-5">
                        <label
                            for="quantity"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Quantity
                        </label>

                        <input
                            type="number"
                            id="quantity"
                            name="quantity"
                            value="{{ old('quantity') }}"
                            min="0.01"
                            step="0.01"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required
                        >

                        @error('quantity')
                            <p class="text-sm text-red-600 mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Reason --}}
                    <div class="mb-6">
                        <label
                            for="reason"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Reason
                        </label>

                        <textarea
                            id="reason"
                            name="reason"
                            rows="4"
                            placeholder="Enter reason for stock adjustment..."
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >{{ old('reason') }}</textarea>

                        @error('reason')
                            <p class="text-sm text-red-600 mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Buttons --}}
                    <div class="flex items-center justify-end gap-3">

                        <a
                            href="{{ route('inventory.index') }}"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                        >
                            Adjust Stock
                        </button>

                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

</x-app-layout>
