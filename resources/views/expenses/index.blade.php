
<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Expenses
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Track and manage business expenses
                </p>
            </div>

            <a
                href="{{ route('expenses.create') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700"
            >
                + Add Expense
            </a>
        </div>
    </x-slot>

    <div class="py-8">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if (session('success'))

                <div class="mb-6 rounded-md bg-green-50 border border-green-200 p-4">
                    <p class="text-sm text-green-700">
                        {{ session('success') }}
                    </p>
                </div>

            @endif


            {{-- Summary --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                {{-- Total Expenses --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">

                    <p class="text-sm font-medium text-gray-500">
                        Total Expenses
                    </p>

                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        Rs. {{ number_format((float) $totalExpenses, 2) }}
                    </p>

                </div>


                {{-- Expense Count --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">

                    <p class="text-sm font-medium text-gray-500">
                        Expense Records
                    </p>

                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        {{ $expenses->count() }}
                    </p>

                </div>

            </div>


            {{-- Expenses Table --}}
            <div class="bg-white shadow-sm sm:rounded-lg">

                <div class="p-6">

                    <div class="flex items-center justify-between mb-5">

                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                Expense List
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                All recorded business expenses
                            </p>
                        </div>

                    </div>


                    @if ($expenses->count())

                        <div class="overflow-x-auto">

                            <table class="min-w-full border border-gray-200">

                                <thead class="bg-gray-50">

                                    <tr>

                                        <th class="border px-4 py-3 text-left text-sm font-semibold text-gray-700">
                                            Date
                                        </th>

                                        <th class="border px-4 py-3 text-left text-sm font-semibold text-gray-700">
                                            Title
                                        </th>

                                        <th class="border px-4 py-3 text-left text-sm font-semibold text-gray-700">
                                            Category
                                        </th>

                                        <th class="border px-4 py-3 text-right text-sm font-semibold text-gray-700">
                                            Amount
                                        </th>

                                        <th class="border px-4 py-3 text-left text-sm font-semibold text-gray-700">
                                            Description
                                        </th>

                                        <th class="border px-4 py-3 text-center text-sm font-semibold text-gray-700">
                                            Actions
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    @foreach ($expenses as $expense)

                                        <tr class="hover:bg-gray-50">

                                            {{-- Date --}}
                                            <td class="border px-4 py-3 whitespace-nowrap">
                                                {{ $expense->expense_date?->format('Y-m-d') }}
                                            </td>


                                            {{-- Title --}}
                                            <td class="border px-4 py-3 font-medium text-gray-800">
                                                {{ $expense->title }}
                                            </td>


                                            {{-- Category --}}
                                            <td class="border px-4 py-3">
                                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                                                    {{ $expense->category }}
                                                </span>
                                            </td>


                                            {{-- Amount --}}
                                            <td class="border px-4 py-3 text-right font-semibold text-gray-800 whitespace-nowrap">
                                                Rs. {{ number_format((float) $expense->amount, 2) }}
                                            </td>


                                            {{-- Description --}}
                                            <td class="border px-4 py-3 text-gray-600">
                                                {{ $expense->description ?: '-' }}
                                            </td>


                                            {{-- Actions --}}
                                            <td class="border px-4 py-3">

                                                <div class="flex items-center justify-center gap-2">

                                                    {{-- View --}}
                                                    <a
                                                        href="{{ route('expenses.show', $expense) }}"
                                                        class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-medium rounded-md hover:bg-gray-200"
                                                    >
                                                        View
                                                    </a>


                                                    {{-- Edit --}}
                                                    <a
                                                        href="{{ route('expenses.edit', $expense) }}"
                                                        class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 text-xs font-medium rounded-md hover:bg-blue-200"
                                                    >
                                                        Edit
                                                    </a>


                                                    {{-- Delete --}}
                                                    <form
                                                        method="POST"
                                                        action="{{ route('expenses.destroy', $expense) }}"
                                                        onsubmit="return confirm('Are you sure you want to delete this expense?');"
                                                    >

                                                        @csrf
                                                        @method('DELETE')

                                                        <button
                                                            type="submit"
                                                            class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 text-xs font-medium rounded-md hover:bg-red-200"
                                                        >
                                                            Delete
                                                        </button>

                                                    </form>

                                                </div>

                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    @else

                        {{-- Empty State --}}
                        <div class="rounded-md bg-gray-50 border border-gray-200 p-8 text-center">

                            <p class="text-gray-600 font-medium">
                                No expenses found.
                            </p>

                            <p class="text-sm text-gray-500 mt-1">
                                Start by adding your first business expense.
                            </p>

                            <a
                                href="{{ route('expenses.create') }}"
                                class="inline-flex mt-4 px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700"
                            >
                                + Add Expense
                            </a>

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>

</x-app-layout>

