<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Customers
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
                            Customer Management
                        </h3>

                        <a
                            href="{{ route('customers.create') }}"
                            class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700"
                        >
                            + Add Customer
                        </a>
                    </div>

                    @if ($customers->count())

                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr class="border-b bg-gray-50">
                                        <th class="text-left p-3">Name</th>
                                        <th class="text-left p-3">Email</th>
                                        <th class="text-left p-3">Phone</th>
                                        <th class="text-left p-3">Address</th>
                                        <th class="text-right p-3">Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($customers as $customer)
                                        <tr class="border-b">

                                            <td class="p-3">
                                                {{ $customer->name }}
                                            </td>

                                            <td class="p-3">
                                                {{ $customer->email ?? '-' }}
                                            </td>

                                            <td class="p-3">
                                                {{ $customer->phone ?? '-' }}
                                            </td>

                                            <td class="p-3">
                                                {{ $customer->address ?? '-' }}
                                            </td>

                                            <td class="p-3 text-right">
                                                <a
                                                    href="{{ route('customers.show', $customer) }}"
                                                    class="text-blue-600 mr-3"
                                                >
                                                    View
                                                </a>

                                                <a
                                                    href="{{ route('customers.edit', $customer) }}"
                                                    class="text-green-600 mr-3"
                                                >
                                                    Edit
                                                </a>

                                                <form
                                                    action="{{ route('customers.destroy', $customer) }}"
                                                    method="POST"
                                                    class="inline"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="text-red-600"
                                                        onclick="return confirm('Are you sure you want to delete this customer?')"
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
                            No customers available yet.
                        </div>

                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>