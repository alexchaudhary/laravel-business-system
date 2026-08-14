<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Products
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">
                            Product Management
                        </h3>

                        <a
                            href="{{ route('products.create') }}"
                            class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700"
                        >
                            + Add Product
                        </a>
                    </div>

                    @if ($products->count())

                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr class="border-b bg-gray-50">
                                        <th class="text-left p-3">Name</th>
                                        <th class="text-left p-3">SKU</th>
                                        <th class="text-left p-3">Category</th>
                                        <th class="text-left p-3">Purchase Price</th>
                                        <th class="text-left p-3">Selling Price</th>
                                        <th class="text-left p-3">Stock</th>
                                        <th class="text-left p-3">Status</th>
                                        <th class="text-right p-3">Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($products as $product)
                                        <tr class="border-b">

                                            <td class="p-3">
                                                {{ $product->name }}
                                            </td>

                                            <td class="p-3">
                                                {{ $product->sku }}
                                            </td>

                                            <td class="p-3">
                                                {{ $product->category ?? '-' }}
                                            </td>

                                            <td class="p-3">
                                                Rs. {{ number_format($product->purchase_price, 2) }}
                                            </td>

                                            <td class="p-3">
                                                Rs. {{ number_format($product->selling_price, 2) }}
                                            </td>

                                            <td class="p-3">
                                                {{ $product->stock_quantity }}
                                            </td>

                                            <td class="p-3">
                                                @if ($product->is_active)
                                                    <span class="text-green-600 font-medium">
                                                        Active
                                                    </span>
                                                @else
                                                    <span class="text-red-600 font-medium">
                                                        Inactive
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="p-3 text-right whitespace-nowrap">
                                                <a
                                                    href="{{ route('products.show', $product) }}"
                                                    class="text-blue-600 mr-3"
                                                >
                                                    View
                                                </a>

                                                <a
                                                    href="{{ route('products.edit', $product) }}"
                                                    class="text-green-600 mr-3"
                                                >
                                                    Edit
                                                </a>

                                                <form
                                                    action="{{ route('products.destroy', $product) }}"
                                                    method="POST"
                                                    class="inline"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="text-red-600"
                                                        onclick="return confirm('Are you sure you want to delete this product?')"
                                                    >
                                                        Delete
                                                    </button>
                                                </form>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    @else

                        <div class="text-gray-600">
                            No products available yet.
                        </div>

                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>