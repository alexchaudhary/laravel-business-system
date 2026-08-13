<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Customer
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <h3 class="text-2xl font-bold mb-6">
                        Edit Customer
                    </h3>

                    @if ($errors->any())
                        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('customers.update', $customer) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="name" class="block font-medium text-sm text-gray-700">
                                Name
                            </label>

                            <input
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old('name', $customer->name) }}"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            >
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block font-medium text-sm text-gray-700">
                                Email
                            </label>

                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email', $customer->email) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            >
                        </div>

                        <div class="mb-4">
                            <label for="phone" class="block font-medium text-sm text-gray-700">
                                Phone
                            </label>

                            <input
                                id="phone"
                                type="text"
                                name="phone"
                                value="{{ old('phone', $customer->phone) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            >
                        </div>

                        <div class="mb-6">
                            <label for="address" class="block font-medium text-sm text-gray-700">
                                Address
                            </label>

                            <textarea
                                id="address"
                                name="address"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            >{{ old('address', $customer->address) }}</textarea>
                        </div>

                        <div class="flex items-center gap-4">
                            <button
                                type="submit"
                                class="px-5 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700"
                            >
                                Update Customer
                            </button>

                            <a
                                href="{{ route('customers.index') }}"
                                class="px-5 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
                            >
                                Cancel
                            </a>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>