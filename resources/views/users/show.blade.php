<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                User Details
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                View system user information.
            </p>
        </div>
    </x-slot>

    <div class="py-12">

        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg">

                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        User Information
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        Account details and assigned role.
                    </p>
                </div>

                <div class="p-6 space-y-6">

                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Name
                        </p>

                        <p class="mt-1 text-gray-900">
                            {{ $user->name }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Email
                        </p>

                        <p class="mt-1 text-gray-900">
                            {{ $user->email }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Role
                        </p>

                        <p class="mt-1 text-gray-900">
                            {{ ucfirst($user->role) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Created
                        </p>

                        <p class="mt-1 text-gray-900">
                            {{ $user->created_at->format('d M Y') }}
                        </p>
                    </div>

                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">

                    <a
                        href="{{ route('users.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-200 rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-300"
                    >
                        Back
                    </a>

                    @if ($user->id !== auth()->id())
                        <a
                            href="{{ route('users.edit', $user) }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 rounded-md font-semibold text-sm text-white hover:bg-indigo-700"
                        >
                            Edit User
                        </a>
                    @endif

                </div>

            </div>

        </div>

    </div>

</x-app-layout>