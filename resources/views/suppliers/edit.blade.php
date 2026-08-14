<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Supplier
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">
                                Name
                            </label>

                            <input
                                type="text"
                                name="name"
                                value="{{ old('name', $supplier->name) }}"
                                class="block mt-1 w-full border-gray-300 rounded-md"
                                required
                            >

                            @error('name')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">
                                Company
                            </label>

                            <input
                                type="text"
                                name="company"
                                value="{{ old('company', $supplier->company) }}"
                                class="block mt-1 w-full border-gray-300 rounded-md"
                            >
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">
                                Email
                            </label>

                            <input
                                type="email"
                                name="email"
                                value="{{ old('email', $supplier->email) }}"
                                class="block mt-1 w-full border-gray-300 rounded-md"
                            >
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">
                                Phone
                            </label>

                            <input
                                type="text"
                                name="phone"
                                value="{{ old('phone', $supplier->phone) }}"
                                class="block mt-1 w-full border-gray-300 rounded-md"
                                required
                            >

                            @error('phone')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">
                                Address
                            </label>

                            <textarea
                                name="address"
                                rows="3"
                                class="block mt-1 w-full border-gray-300 rounded-md"
                            >{{ old('address', $supplier->address) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">
                                Description
                            </label>

                            <textarea
                                name="description"
                                rows="3"
                                class="block mt-1 w-full border-gray-300 rounded-md"
                            >{{ old('description', $supplier->description) }}</textarea>
                        </div>

                        <div class="mb-6">
                            <label class="inline-flex items-center">
                                <input
                                    type="checkbox"
                                    name="active"
                                    value="1"
                                    {{ old('active', $supplier->active) ? 'checked' : '' }}
                                    class="rounded border-gray-300"
                                >

                                <span class="ml-2 text-sm text-gray-700">
                                    Active Supplier
                                </span>
                            </label>
                        </div>

                        <div class="flex justify-end gap-3">
                            <a
                                href="{{ route('suppliers.index') }}"
                                class="px-4 py-2 border border-gray-300 rounded-md"
                            >
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700"
                            >
                                Update Supplier
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>