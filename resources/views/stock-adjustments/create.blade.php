<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Stock Adjustment
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <h3 class="text-lg font-semibold mb-1">
                        Adjust Stock
                    </h3>

                    <p class="text-sm text-gray-500 mb-6">
                        Add or remove stock from a product.
                    </p>

                    @if ($errors->any())
                        <div class="mb-6 rounded-md bg-red-50 border border-red-200 p-4">
                            <ul class="list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('stock-adjustments.store') }}">
                        @csrf

                        {{-- Product --}}
                        <div class="mb-5">
                            <label class="block font-medium text-sm text-gray-700 mb-2">
                                Product
                            </label>

                            <select
                                name="product_id"
                                class="block w-full border-gray-300 rounded-md shadow-sm"
                                required
                            >
                                <option value="">Select Product</option>

                                @foreach ($products as $product)
                                    <option
                                        value="{{ $product->id }}"
                                        {{ old('product_id') == $product->id ? 'selected' : '' }}
                                    >
                                        {{ $product->name }}
                                        — Current Stock: {{ number_format((float) $product->stock_quantity, 2) }}
                                    </option>
                                @endforeach
                            </select>

                            @error('product_id')
                                <p class="text-red-600 text-sm mt-1">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Adjustment Type --}}
                        <div class="mb-5">
                            <label class="block font-medium text-sm text-gray-700 mb-2">
                                Adjustment Type
                            </label>

                            <select
                                name="type"
                                class="block w-full border-gray-300 rounded-md shadow-sm"
                                required
                            >
                                <option value="">Select Type</option>
                                <option value="in" {{ old('type') === 'in' ? 'selected' : '' }}>
                                    Stock In
                                </option>
                                <option value="out" {{ old('type') === 'out' ? 'selected' : '' }}>
                                    Stock Out
                                </option>
                            </select>

                            @error('type')
                                <p class="text-red-600 text-sm mt-1">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Quantity --}}
                        <div class="mb-5">
                            <label class="block font-medium text-sm text-gray-700 mb-2">
                                Quantity
                            </label>

                            <input
                                type="number"
                                name="quantity"
                                value="{{ old('quantity') }}"
                                min="0.01"
                                step="0.01"
                                class="block w-full border-gray-300 rounded-md shadow-sm"
                                required
                            >

                            @error('quantity')
                                <p class="text-red-600 text-sm mt-1">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Note --}}
                        <div class="mb-6">
                            <label class="block font-medium text-sm text-gray-700 mb-2">
                                Note
                            </label>

                            <textarea
                                name="note"
                                rows="4"
                                class="block w-full border-gray-300 rounded-md shadow-sm"
                                placeholder="Example: Damaged item, manual stock correction, returned item..."
                            >{{ old('note') }}</textarea>

                            @error('note')
                                <p class="text-red-600 text-sm mt-1">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Buttons --}}
                        <div class="flex items-center gap-3">

                            <a
                                href="{{ route('stock-adjustments.index') }}"
                                class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
                            >
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="px-5 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700"
                            >
                                Save Adjustment
                            </button>

                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>

