<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-6 text-center text-green-500" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="max-w-sm mx-auto p-8 rounded-lg">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-lg font-medium text-gray-700" />
            <x-text-input id="email" class="block mt-3 w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500" />
        </div>

        <!-- Password -->
        <div class="mt-6">
            <x-input-label for="password" :value="__('Password')" class="text-lg font-medium text-gray-700" />

            <x-text-input id="password" class="block mt-3 w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-6">
            <label for="remember_me" class="inline-flex items-center text-gray-700">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                <span class="ml-2 text-sm">{{ __('Remember me') }}</span>
            </label>
        </div>

        <!-- Login Button -->
        <div class="mt-6">
            <x-primary-button class="w-full py-3 text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200 flex items-center justify-center h-12">
                {{ __('Log in') }}
            </x-primary-button>
        </div>

        <!-- Forgot Password and Create Account Links -->
        <div class="flex justify-between mt-6 mb-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-800 transition duration-150" href="{{ route('password.request') }}">
                {{ __('Forgot your password?') }}
            </a>

            <a class="underline text-sm text-gray-600 hover:text-gray-800 transition duration-150" href="{{ route('register') }}">
                {{ __('Create Account') }}
            </a>
        </div>
    </form>
</x-guest-layout>
