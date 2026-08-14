<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Add Product
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form action="{{ route('products.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Product Name -->
                            <div>
                                <label class="block font-medium mb-2">
                                    Product Name
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    class="w-full rounded-md border-gray-300"
                                >

                                @error('name')
                                    <p class="text-red-600 text-sm mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- SKU -->
                            <div>
                                <label class="block font-medium mb-2">
                                    SKU
                                </label>

                                <input
                                    type="text"
                                    name="sku"
                                    value="{{ old('sku') }}"
                                    class="w-full rounded-md border-gray-300"
                                >

                                @error('sku')
                                    <p class="text-red-600 text-sm mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Category -->
                            <div>
                                <label class="block font-medium mb-2">
                                    Category
                                </label>

                                <input
                                    type="text"
                                    name="category"
                                    value="{{ old('category') }}"
                                    class="w-full rounded-md border-gray-300"
                                >

                                @error('category')
                                    <p class="text-red-600 text-sm mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Stock Quantity -->
                            <div>
                                <label class="block font-medium mb-2">
                                    Stock Quantity
                                </label>

                                <input
                                    type="number"
                                    name="stock_quantity"
                                    value="{{ old('stock_quantity', 0) }}"
                                    min="0"
                                    step="0.01"
                                    class="w-full rounded-md border-gray-300"
                                >

                                @error('stock_quantity')
                                    <p class="text-red-600 text-sm mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Low Stock Threshold -->
                            <div>
                                <label class="block font-medium mb-2">
                                    Low Stock Threshold
                                </label>

                                <input
                                    type="number"
                                    name="low_stock_threshold"
                                    value="{{ old('low_stock_threshold', 5) }}"
                                    min="0"
                                    step="0.01"
                                    class="w-full rounded-md border-gray-300"
                                    required
                                >

                                @error('low_stock_threshold')
                                    <p class="text-red-600 text-sm mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Purchase Price -->
                            <div>
                                <label class="block font-medium mb-2">
                                    Purchase Price
                                </label>

                                <input
                                    type="number"
                                    name="purchase_price"
                                    value="{{ old('purchase_price') }}"
                                    step="0.01"
                                    min="0"
                                    class="w-full rounded-md border-gray-300"
                                >

                                @error('purchase_price')
                                    <p class="text-red-600 text-sm mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Selling Price -->
                            <div>
                                <label class="block font-medium mb-2">
                                    Selling Price
                                </label>

                                <input
                                    type="number"
                                    name="selling_price"
                                    value="{{ old('selling_price') }}"
                                    step="0.01"
                                    min="0"
                                    class="w-full rounded-md border-gray-300"
                                >

                                @error('selling_price')
                                    <p class="text-red-600 text-sm mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                        </div>

                        <!-- Description -->
                        <div class="mt-6">
                            <label class="block font-medium mb-2">
                                Description
                            </label>

                            <textarea
                                name="description"
                                rows="4"
                                class="w-full rounded-md border-gray-300"
                            >{{ old('description') }}</textarea>

                            @error('description')
                                <p class="text-red-600 text-sm mt-1">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mt-6">
                            <label class="inline-flex items-center">
                                <input
                                    type="checkbox"
                                    name="is_active"
                                    value="1"
                                    {{ old('is_active', true) ? 'checked' : '' }}
                                    class="rounded border-gray-300"
                                >

                                <span class="ml-2">
                                    Active Product
                                </span>
                            </label>
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-end gap-4 mt-6">

                            <a
                                href="{{ route('products.index') }}"
                                class="px-4 py-2 border rounded-md"
                            >
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="bg-gray-800 text-white px-5 py-2 rounded-md hover:bg-gray-700"
                            >
                                Save Product
                            </button>

                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
