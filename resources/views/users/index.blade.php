<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between">

            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Users
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Manage system users and their roles.
                </p>
            </div>

            {{-- Add User --}}
            <a
                href="{{ route('users.create') }}"
                class="inline-flex items-center px-5 py-2.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition"
            >
                + Add User
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


            {{-- Users Table --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">


                {{-- Table Header --}}
                <div class="p-6 border-b border-gray-200">

                    <h3 class="text-lg font-semibold text-gray-800">
                        System Users
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        View and manage registered users.
                    </p>

                </div>


                {{-- Table --}}
                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">


                        {{-- Table Head --}}
                        <thead class="bg-gray-50">

                            <tr>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    #
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Name
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Email
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Role
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Created
                                </th>

                                {{-- Actions Header --}}
                                <th class="w-[320px] px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        {{-- Table Body --}}
                        <tbody class="bg-white divide-y divide-gray-200">


                            @forelse($users as $user)


                                <tr class="hover:bg-gray-50">


                                    {{-- Number --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $users->firstItem() + $loop->index }}
                                    </td>


                                    {{-- Name --}}
                                    <td class="px-6 py-4 whitespace-nowrap">

                                        <div class="font-medium text-gray-900">
                                            {{ $user->name }}
                                        </div>

                                    </td>


                                    {{-- Email --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $user->email }}
                                    </td>


                                    {{-- Role --}}
                                    <td class="px-6 py-4 whitespace-nowrap">

                                        @if($user->role === 'admin')

                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                                Admin
                                            </span>

                                        @else

                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                User
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Created --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $user->created_at?->format('d M Y') }}
                                    </td>


                                    {{-- Actions --}}
                                    <td class="w-[320px] px-6 py-4 whitespace-nowrap">

                                        {{-- Fixed Action Area --}}
                                        <div class="w-[288px] ml-auto flex items-center gap-2">


                                            {{-- View --}}
                                            <a
                                                href="{{ route('users.show', $user) }}"
                                                class="w-[80px] h-10 shrink-0 inline-flex items-center justify-center bg-gray-100 text-gray-700 rounded-md font-medium hover:bg-gray-200 transition"
                                            >
                                                View
                                            </a>


                                            {{-- Edit --}}
                                            <a
                                                href="{{ route('users.edit', $user) }}"
                                                class="w-[80px] h-10 shrink-0 inline-flex items-center justify-center bg-indigo-600 text-white rounded-md font-medium hover:bg-indigo-700 transition"
                                            >
                                                Edit
                                            </a>


                                            {{-- Delete / Current User --}}
                                            @if($user->id !== auth()->id())


                                                <form
                                                    method="POST"
                                                    action="{{ route('users.destroy', $user) }}"
                                                    onsubmit="return confirm('Are you sure you want to delete this user?');"
                                                    class="w-[112px] h-10 shrink-0 m-0 p-0"
                                                >

                                                    @csrf
                                                    @method('DELETE')


                                                    <button
                                                        type="submit"
                                                        class="w-full h-full inline-flex items-center justify-center bg-red-600 text-white rounded-md font-medium hover:bg-red-700 transition"
                                                    >
                                                        Delete
                                                    </button>

                                                </form>


                                            @else


                                                <span
                                                    class="w-[112px] h-10 shrink-0 inline-flex items-center justify-center bg-gray-100 text-gray-400 rounded-md font-medium cursor-not-allowed whitespace-nowrap"
                                                >
                                                    Current User
                                                </span>


                                            @endif


                                        </div>

                                    </td>


                                </tr>


                            @empty


                                {{-- No Users --}}
                                <tr>

                                    <td
                                        colspan="6"
                                        class="px-6 py-12 text-center"
                                    >

                                        <div class="text-gray-500">
                                            No users found.
                                        </div>


                                        <a
                                            href="{{ route('users.create') }}"
                                            class="inline-flex items-center mt-4 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                                        >
                                            Add First User
                                        </a>

                                    </td>

                                </tr>


                            @endforelse


                        </tbody>

                    </table>

                </div>


                {{-- Pagination --}}
                @if($users->hasPages())

                    <div class="px-6 py-4 border-t border-gray-200">

                        {{ $users->links() }}

                    </div>

                @endif


            </div>

        </div>

    </div>

</x-app-layout>