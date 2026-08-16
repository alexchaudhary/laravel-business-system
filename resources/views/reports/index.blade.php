
<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Business Reports
        </h2>
    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Page Header --}}
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800">
                    Financial Summary
                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Overview of sales, purchases, expenses and profit/loss.
                </p>
            </div>


            {{-- Filter Report Card --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">

                <h3 class="text-lg font-semibold text-gray-800 mb-5">
                    Filter Report
                </h3>


                <form method="GET" action="{{ route('reports.index') }}">

                    {{-- Date Fields --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- From Date --}}
                        <div>
                            <label
                                for="from_date"
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                From Date
                            </label>

                            <input
                                type="date"
                                id="from_date"
                                name="from_date"
                                value="{{ $fromDate ?? request('from_date') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>


                        {{-- To Date --}}
                        <div>
                            <label
                                for="to_date"
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                To Date
                            </label>

                            <input
                                type="date"
                                id="to_date"
                                name="to_date"
                                value="{{ $toDate ?? request('to_date') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>

                    </div>


                    {{-- BUTTON ROW --}}
                    <div
                        style="
                            display: flex;
                            align-items: center;
                            gap: 12px;
                            margin-top: 24px;
                        "
                    >

                        {{-- FILTER REPORT BUTTON --}}
                        <button
                            type="submit"
                            style="
                                display: inline-flex;
                                align-items: center;
                                justify-content: center;
                                min-width: 160px;
                                height: 44px;
                                padding: 0 24px;
                                background-color: #4f46e5;
                                color: #ffffff;
                                border: 1px solid #4f46e5;
                                border-radius: 6px;
                                font-size: 14px;
                                font-weight: 600;
                                text-transform: uppercase;
                                letter-spacing: 0.05em;
                                cursor: pointer;
                                box-shadow: 0 1px 2px rgba(0,0,0,0.05);
                            "
                        >
                            Filter Report
                        </button>


                        {{-- CLEAR BUTTON --}}
                        <a
                            href="{{ route('reports.index') }}"
                            style="
                                display: inline-flex;
                                align-items: center;
                                justify-content: center;
                                min-width: 110px;
                                height: 44px;
                                padding: 0 24px;
                                background-color: #e5e7eb;
                                color: #374151;
                                border: 1px solid #d1d5db;
                                border-radius: 6px;
                                font-size: 14px;
                                font-weight: 600;
                                text-transform: uppercase;
                                letter-spacing: 0.05em;
                                text-decoration: none;
                                cursor: pointer;
                                box-shadow: 0 1px 2px rgba(0,0,0,0.05);
                            "
                        >
                            Clear
                        </a>

                    </div>

                </form>

            </div>


            {{-- Active Filter Message --}}
            @if(($fromDate ?? null) || ($toDate ?? null))

                <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-lg p-4 mb-6">

                    <div class="font-semibold">
                        Report Filter Applied
                    </div>

                    <div class="text-sm mt-1">

                        @if($fromDate ?? null)
                            <span>
                                From:
                                <strong>{{ $fromDate }}</strong>
                            </span>
                        @endif

                        @if(($fromDate ?? null) && ($toDate ?? null))
                            <span class="mx-2">
                                to
                            </span>
                        @endif

                        @if($toDate ?? null)
                            <span>
                                To:
                                <strong>{{ $toDate }}</strong>
                            </span>
                        @endif

                    </div>

                </div>

            @endif


            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                {{-- Total Sales --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">

                    <div class="text-sm text-gray-500">
                        Total Sales
                    </div>

                    <div class="text-2xl font-bold text-green-600 mt-2">
                        Rs. {{ number_format((float) $totalSales, 2) }}
                    </div>

                </div>


                {{-- Total Purchases --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">

                    <div class="text-sm text-gray-500">
                        Total Purchases
                    </div>

                    <div class="text-2xl font-bold text-blue-600 mt-2">
                        Rs. {{ number_format((float) $totalPurchases, 2) }}
                    </div>

                </div>


                {{-- Total Expenses --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">

                    <div class="text-sm text-gray-500">
                        Total Expenses
                    </div>

                    <div class="text-2xl font-bold text-red-600 mt-2">
                        Rs. {{ number_format((float) $totalExpenses, 2) }}
                    </div>

                </div>


                {{-- Profit / Loss --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">

                    <div class="text-sm text-gray-500">
                        Profit / Loss
                    </div>

                    <div
                        class="text-2xl font-bold mt-2
                        {{ $profit >= 0 ? 'text-green-600' : 'text-red-600' }}"
                    >
                        Rs. {{ number_format((float) $profit, 2) }}
                    </div>

                    <div class="text-xs text-gray-500 mt-1">
                        Sales − Purchases − Expenses
                    </div>

                </div>

            </div>


            {{-- Calculation Summary --}}
            <div class="bg-white shadow-sm sm:rounded-lg mt-6">

                <div class="p-6">

                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        Calculation Summary
                    </h3>

                    <div class="space-y-3 text-sm">

                        {{-- Sales --}}
                        <div class="flex justify-between border-b pb-3">

                            <span class="text-gray-600">
                                Total Sales
                            </span>

                            <span class="font-semibold">
                                Rs. {{ number_format((float) $totalSales, 2) }}
                            </span>

                        </div>


                        {{-- Purchases --}}
                        <div class="flex justify-between border-b pb-3">

                            <span class="text-gray-600">
                                Less: Purchases
                            </span>

                            <span class="font-semibold text-blue-600">
                                Rs. {{ number_format((float) $totalPurchases, 2) }}
                            </span>

                        </div>


                        {{-- Expenses --}}
                        <div class="flex justify-between border-b pb-3">

                            <span class="text-gray-600">
                                Less: Expenses
                            </span>

                            <span class="font-semibold text-red-600">
                                Rs. {{ number_format((float) $totalExpenses, 2) }}
                            </span>

                        </div>


                        {{-- Profit / Loss --}}
                        <div class="flex justify-between pt-2">

                            <span class="font-bold text-gray-800">
                                Profit / Loss
                            </span>

                            <span
                                class="font-bold
                                {{ $profit >= 0 ? 'text-green-600' : 'text-red-600' }}"
                            >
                                Rs. {{ number_format((float) $profit, 2) }}
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
