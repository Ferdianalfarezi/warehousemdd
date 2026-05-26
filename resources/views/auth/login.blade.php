<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - WarehousMDD</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<style>
    .logo-image {
        width: 440px;
        height: 120px;
        object-fit: contain;
        transition: transform 0.3s ease;
    }
</style>

<body class="bg-black">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full">
            <!-- Logo/Title -->
            <div class="text-center mb-8">
                <img src="{{ asset('images/logomdd.png') }}" alt="Logo" class="logo-image">
                <p class="text-white">Warehouse Management System</p>
            </div>

            <!-- Login Card -->
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <h2 class="text-2xl font-bold text-black mb-6">Sign In</h2>

                <!-- Session Expired -->
                @if (session('status'))
                    <div class="mb-4 flex items-center gap-2 bg-yellow-50 border border-yellow-300 text-yellow-800 text-sm font-medium px-4 py-3 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                        </svg>
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-300 text-red-700 text-sm px-4 py-3 rounded-lg">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Username -->
                    <div class="mb-4">
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <input
                            id="username"
                            type="text"
                            name="username"
                            value="{{ old('username') }}"
                            required
                            autofocus
                            autocomplete="username"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition duration-200 @error('username') border-red-500 @enderror"
                            placeholder="Enter your username"
                        >
                        @error('username')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition duration-200 @error('password') border-red-500 @enderror"
                            placeholder="Enter your password"
                        >
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full bg-black text-white py-3 rounded-lg font-semibold hover:bg-gray-800 transition duration-200 transform hover:scale-105"
                    >
                        Sign In
                    </button>
                </form>
            </div>

            <!-- Copyright -->
            <div class="text-center mt-6">
                <p style="margin: 0; font-size: 0.875rem; color: #888888;">
                    &copy; {{ date('Y') }} <strong style="color: #ffffff;"><i>STEP IT DEPT</i></strong> - All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>