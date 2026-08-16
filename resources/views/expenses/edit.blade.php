<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Expense
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form method="POST" action="{{ route('expenses.update', $expense) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">
                                Expense Date
                            </label>

                            <input
                                type="date"
                                name="expense_date"
                                value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}"
                                class="block mt-1 w-full border-gray-300 rounded-md"
                                required
                            >
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">
                                Title
                            </label>

                            <input
                                type="text"
                                name="title"
                                value="{{ old('title', $expense->title) }}"
                                class="block mt-1 w-full border-gray-300 rounded-md"
                                required
                            >
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">
                                Category
                            </label>

                            <select
                                name="category"
                                class="block mt-1 w-full border-gray-300 rounded-md"
                                required
                            >
                                @foreach (['Utilities', 'Rent', 'Salary', 'Transport', 'Office', 'Marketing', 'Other'] as $category)
                                    <option value="{{ $category }}"
                                        {{ old('category', $expense->category) === $category ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">
                                Amount
                            </label>

                            <input
                                type="number"
                                name="amount"
                                step="0.01"
                                min="0.01"
                                value="{{ old('amount', $expense->amount) }}"
                                class="block mt-1 w-full border-gray-300 rounded-md"
                                required
                            >
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">
                                Description
                            </label>

                            <textarea
                                name="description"
                                rows="4"
                                class="block mt-1 w-full border-gray-300 rounded-md"
                            >{{ old('description', $expense->description) }}</textarea>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('expenses.index') }}"
                               class="px-4 py-2 bg-gray-200 text-gray-800 rounded">
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded"
                            >
                                Update Expense
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>