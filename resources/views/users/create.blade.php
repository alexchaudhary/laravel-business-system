<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Add User
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Create a new system user and assign a role.
            </p>
        </div>
    </x-slot>

    <div class="py-12">

        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg">

                {{-- Header --}}
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        User Information
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        Enter the user's account details below.
                    </p>
                </div>

                {{-- Form --}}
                <form method="POST" action="{{ route('users.store') }}">

                    @csrf

                    <div class="p-6 space-y-6">

                        {{-- Name --}}
                        <div>
                            <label
                                for="name"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Name
                            </label>

                            <input
                                id="name"
                                name="name"
                                type="text"
                                value="{{ old('name') }}"
                                required
                                autofocus
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >

                            @error('name')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>


                        {{-- Email --}}
                        <div>
                            <label
                                for="email"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Email
                            </label>

                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >

                            @error('email')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>


                        {{-- Password --}}
                        <div>
                            <label
                                for="password"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Password
                            </label>

                            <div class="relative mt-1">

                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    required
                                    minlength="8"
                                    class="block w-full rounded-md border-gray-300 pr-20 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                <button
                                    type="button"
                                    onclick="togglePassword('password', this)"
                                    class="absolute inset-y-0 right-0 px-4 text-sm font-medium text-indigo-600 hover:text-indigo-800"
                                >
                                    Show
                                </button>

                            </div>

                            <p class="mt-1 text-xs text-gray-500">
                                Minimum 8 characters.
                            </p>

                            @error('password')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>


                        {{-- Confirm Password --}}
                        <div>
                            <label
                                for="password_confirmation"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Confirm Password
                            </label>

                            <div class="relative mt-1">

                                <input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    required
                                    minlength="8"
                                    class="block w-full rounded-md border-gray-300 pr-20 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                <button
                                    type="button"
                                    onclick="togglePassword('password_confirmation', this)"
                                    class="absolute inset-y-0 right-0 px-4 text-sm font-medium text-indigo-600 hover:text-indigo-800"
                                >
                                    Show
                                </button>

                            </div>

                            @error('password_confirmation')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>


                        {{-- Role --}}
                        <div>
                            <label
                                for="role"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Role
                            </label>

                            <select
                                id="role"
                                name="role"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">
                                    Select Role
                                </option>

                                <option
                                    value="user"
                                    {{ old('role') === 'user' ? 'selected' : '' }}
                                >
                                    User
                                </option>

                                <option
                                    value="admin"
                                    {{ old('role') === 'admin' ? 'selected' : '' }}
                                >
                                    Admin
                                </option>
                            </select>

                            @error('role')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>


                    {{-- Actions --}}
                    <div
                        style="
                            display: flex;
                            justify-content: flex-end;
                            align-items: center;
                            gap: 12px;
                            padding: 20px 24px;
                            background: #f9fafb;
                            border-top: 1px solid #e5e7eb;
                        "
                    >

                        {{-- Cancel --}}
                        <a
                            href="{{ route('users.index') }}"
                            style="
                                display: inline-flex;
                                align-items: center;
                                justify-content: center;
                                padding: 10px 20px;
                                background: #e5e7eb;
                                color: #374151;
                                border: 1px solid #d1d5db;
                                border-radius: 6px;
                                font-weight: 600;
                                font-size: 14px;
                                text-decoration: none;
                            "
                        >
                            Cancel
                        </a>


                        {{-- Create User --}}
                        <button
                            type="submit"
                            style="
                                display: inline-flex !important;
                                align-items: center;
                                justify-content: center;
                                visibility: visible !important;
                                opacity: 1 !important;
                                position: relative;
                                z-index: 50;
                                padding: 10px 20px;
                                background: #4f46e5 !important;
                                color: #ffffff !important;
                                border: 1px solid #4f46e5;
                                border-radius: 6px;
                                font-weight: 600;
                                font-size: 14px;
                                cursor: pointer;
                            "
                        >
                            Create User
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    {{-- Password Show / Hide --}}
    <script>
        function togglePassword(inputId, button) {

            const input = document.getElementById(inputId);

            if (input.type === 'password') {
                input.type = 'text';
                button.textContent = 'Hide';
            } else {
                input.type = 'password';
                button.textContent = 'Show';
            }
        }
    </script>

</x-app-layout>