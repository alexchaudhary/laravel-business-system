<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Expense Details
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">
                            Expense #{{ $expense->id }}
                        </h3>

                        <div class="flex gap-2">
                            <a href="{{ route('expenses.edit', $expense) }}"
                               class="px-4 py-2 bg-blue-600 text-white rounded">
                                Edit
                            </a>

                            <a href="{{ route('expenses.index') }}"
                               class="px-4 py-2 bg-gray-200 text-gray-800 rounded">
                                Back
                            </a>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <p>
                            <strong>Date:</strong>
                            {{ $expense->expense_date->format('Y-m-d') }}
                        </p>

                        <p>
                            <strong>Title:</strong>
                            {{ $expense->title }}
                        </p>

                        <p>
                            <strong>Category:</strong>
                            {{ $expense->category }}
                        </p>

                        <p>
                            <strong>Amount:</strong>
                            Rs. {{ number_format($expense->amount, 2) }}
                        </p>

                        <p>
                            <strong>Description:</strong>
                            {{ $expense->description ?: 'N/A' }}
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>