<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Invoices
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Manage customer invoices and billing records.
                </p>
            </div>

            <a
                href="{{ route('invoices.create') }}"
                class="inline-flex items-center px-5 py-2.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700 transition"
            >
                + Create Invoice
            </a>
        </div>
    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Error Message --}}
            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Invoice Table --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">

                {{-- Table Header --}}
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Invoice Records
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        View and manage all generated invoices.
                    </p>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">
                            <tr>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    #
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Invoice
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Customer
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Invoice Date
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Due Date
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Total
                                </th>

                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Status
                                </th>

                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Actions
                                </th>

                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">

                            @forelse($invoices as $invoice)

                                <tr class="hover:bg-gray-50">

                                    {{-- Number --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $loop->iteration }}
                                    </td>

                                    {{-- Invoice Number --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">
                                            {{ $invoice->invoice_number }}
                                        </div>
                                    </td>

                                    {{-- Customer --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">
                                            {{ $invoice->sale?->customer?->name ?? 'N/A' }}
                                        </div>
                                    </td>

                                    {{-- Invoice Date --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $invoice->invoice_date?->format('d M Y') }}
                                    </td>

                                    {{-- Due Date --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $invoice->due_date?->format('d M Y') ?? '—' }}
                                    </td>

                                    {{-- Total --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                                        {{ number_format((float) $invoice->total_amount, 2) }}
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">

                                        @switch($invoice->status)

                                            @case('draft')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                    Draft
                                                </span>
                                                @break

                                            @case('issued')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                    Issued
                                                </span>
                                                @break

                                            @case('paid')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                    Paid
                                                </span>
                                                @break

                                            @case('partially_paid')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                                    Partially Paid
                                                </span>
                                                @break

                                            @case('overdue')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                    Overdue
                                                </span>
                                                @break

                                            @case('cancelled')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                    Cancelled
                                                </span>
                                                @break

                                            @default
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                    {{ ucfirst($invoice->status) }}
                                                </span>

                                        @endswitch

                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-6 py-4 whitespace-nowrap">

                                        <div class="flex items-center justify-center gap-2">

                                            {{-- View --}}
                                            <a
                                                href="{{ route('invoices.show', $invoice) }}"
                                                class="inline-flex items-center justify-center h-10 px-4 bg-gray-100 text-gray-700 rounded-md font-medium hover:bg-gray-200 transition"
                                            >
                                                View
                                            </a>

                                            {{-- Edit --}}
                                            <a
                                                href="{{ route('invoices.edit', $invoice) }}"
                                                class="inline-flex items-center justify-center h-10 px-4 bg-indigo-600 text-white rounded-md font-medium hover:bg-indigo-700 transition"
                                            >
                                                Edit
                                            </a>

                                            {{-- Delete --}}
                                            <form
                                                method="POST"
                                                action="{{ route('invoices.destroy', $invoice) }}"
                                                onsubmit="return confirm('Are you sure you want to delete this invoice?');"
                                                class="m-0"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center justify-center h-10 px-4 bg-red-600 text-white rounded-md font-medium hover:bg-red-700 transition"
                                                >
                                                    Delete
                                                </button>
                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="8"
                                        class="px-6 py-12 text-center"
                                    >

                                        <div class="text-gray-500">
                                            No invoices found.
                                        </div>

                                        <a
                                            href="{{ route('invoices.create') }}"
                                            class="inline-flex items-center mt-4 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                                        >
                                            Create First Invoice
                                        </a>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>

