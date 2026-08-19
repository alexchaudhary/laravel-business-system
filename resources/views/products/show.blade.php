<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Product Details
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                View product information.
            </p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <h3 class="text-lg font-semibold text-gray-800 mb-6">
                        Product Information
                    </h3>

                    <div class="space-y-4">

                        <div>
                            <p class="text-sm text-gray-500">
                                Name
                            </p>

                            <p class="text-base font-medium text-gray-900">
                                {{ $product->name }}
                            </p>
                        </div>

                        @if(isset($product->price))
                            <div>
                                <p class="text-sm text-gray-500">
                                    Price
                                </p>

                                <p class="text-base text-gray-900">
                                    {{ $product->price }}
                                </p>
                            </div>
                        @endif

                        @if(isset($product->description))
                            <div>
                                <p class="text-sm text-gray-500">
                                    Description
                                </p>

                                <p class="text-base text-gray-900">
                                    {{ $product->description ?? 'N/A' }}
                                </p>
                            </div>
                        @endif

                    </div>

                    <div class="mt-8 flex items-center gap-3">

                        <a
                            href="{{ route('products.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-300"
                        >
                            Back
                        </a>

                        <a
                            href="{{ route('products.edit', $product) }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700"
                        >
                            Edit Product
                        </a>

                    </div>

                </div>
            </div>

        </div>
    </div>

</x-app-layout>