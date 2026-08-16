
<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Add Expense
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Record a new business expense
            </p>
        </div>
    </x-slot>

    <div class="py-8">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg">

                <div class="p-6">

                    {{-- Validation Errors --}}
                    @if ($errors->any())

                        <div class="mb-6 rounded-md bg-red-50 border border-red-200 p-4">

                            <p class="font-medium text-red-700 mb-2">
                                Please fix the following errors:
                            </p>

                            <ul class="list-disc list-inside text-sm text-red-600">

                                @foreach ($errors->all() as $error)

                                    <li>{{ $error }}</li>

                                @endforeach

                            </ul>

                        </div>

                    @endif


                    <form
                        method="POST"
                        action="{{ route('expenses.store') }}"
                    >

                        @csrf


                        {{-- Expense Date --}}
                        <div class="mb-5">

                            <label
                                for="expense_date"
                                class="block text-sm font-medium text-gray-700 mb-1"
                            >
                                Expense Date
                            </label>

                            <input
                                type="date"
                                id="expense_date"
                                name="expense_date"
                                value="{{ old('expense_date', now()->format('Y-m-d')) }}"
                                required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >

                            @error('expense_date')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- Title --}}
                        <div class="mb-5">

                            <label
                                for="title"
                                class="block text-sm font-medium text-gray-700 mb-1"
                            >
                                Title
                            </label>

                            <input
                                type="text"
                                id="title"
                                name="title"
                                value="{{ old('title') }}"
                                placeholder="e.g. Office Rent"
                                required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >

                            @error('title')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- Category --}}
                        <div class="mb-5">

                            <label
                                for="category"
                                class="block text-sm font-medium text-gray-700 mb-1"
                            >
                                Category
                            </label>

                            <select
                                id="category"
                                name="category"
                                required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >

                                <option value="">
                                    Select Category
                                </option>

                                <option value="Rent" {{ old('category') === 'Rent' ? 'selected' : '' }}>
                                    Rent
                                </option>

                                <option value="Utilities" {{ old('category') === 'Utilities' ? 'selected' : '' }}>
                                    Utilities
                                </option>

                                <option value="Salary" {{ old('category') === 'Salary' ? 'selected' : '' }}>
                                    Salary
                                </option>

                                <option value="Transport" {{ old('category') === 'Transport' ? 'selected' : '' }}>
                                    Transport
                                </option>

                                <option value="Office Supplies" {{ old('category') === 'Office Supplies' ? 'selected' : '' }}>
                                    Office Supplies
                                </option>

                                <option value="Marketing" {{ old('category') === 'Marketing' ? 'selected' : '' }}>
                                    Marketing
                                </option>

                                <option value="Maintenance" {{ old('category') === 'Maintenance' ? 'selected' : '' }}>
                                    Maintenance
                                </option>

                                <option value="Other" {{ old('category') === 'Other' ? 'selected' : '' }}>
                                    Other
                                </option>

                            </select>

                            @error('category')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- Amount --}}
                        <div class="mb-5">

                            <label
                                for="amount"
                                class="block text-sm font-medium text-gray-700 mb-1"
                            >
                                Amount
                            </label>

                            <input
                                type="number"
                                id="amount"
                                name="amount"
                                value="{{ old('amount') }}"
                                min="0.01"
                                step="0.01"
                                placeholder="0.00"
                                required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >

                            @error('amount')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- Description --}}
                        <div class="mb-6">

                            <label
                                for="description"
                                class="block text-sm font-medium text-gray-700 mb-1"
                            >
                                Description
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                rows="4"
                                maxlength="2000"
                                placeholder="Optional expense details..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >{{ old('description') }}</textarea>

                            @error('description')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- Buttons --}}
                        <div class="flex items-center gap-3">

                            <a
                                href="{{ route('expenses.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300"
                            >
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700"
                            >
                                Save Expense
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
