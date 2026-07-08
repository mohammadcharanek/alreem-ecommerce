<x-guest-layout>
    <div class="mx-auto w-full max-w-md">
        {{-- Header --}}
        <div class="mb-8">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900">
                Welcome back
            </h2>

            <p class="mt-2 text-sm leading-6 text-gray-600">
                Log in to continue shopping, manage your cart, and track your orders.
            </p>
        </div>

        {{-- Session Status --}}
        <x-auth-session-status class="mb-5 rounded-xl bg-green-50 p-4 text-sm text-green-700 ring-1 ring-green-100" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            {{-- Email Address --}}
            <div>
                <x-input-label for="email" :value="__('Email Address')" class="text-sm font-semibold text-gray-700" />

                <x-text-input
                    id="email"
                    class="mt-2 block w-full rounded-xl border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="you@example.com"
                />

                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            {{-- Password --}}
            <div>
                <div class="flex items-center justify-between gap-4">
                    <x-input-label for="password" :value="__('Password')" class="text-sm font-semibold text-gray-700" />

                    @if (Route::has('password.request'))
                        <a
                            class="text-sm font-medium text-blue-700 hover:text-blue-900"
                            href="{{ route('password.request') }}"
                        >
                            Forgot password?
                        </a>
                    @endif
                </div>

                <x-text-input
                    id="password"
                    class="mt-2 block w-full rounded-xl border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="Enter your password"
                />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            {{-- Remember Me --}}
            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center">
                    <input
                        id="remember_me"
                        type="checkbox"
                        class="rounded border-gray-300 text-blue-700 shadow-sm focus:ring-blue-600"
                        name="remember"
                    >

                    <span class="ms-2 text-sm text-gray-600">
                        {{ __('Remember me') }}
                    </span>
                </label>
            </div>

            {{-- Submit --}}
            <button
                type="submit"
                class="flex w-full items-center justify-center rounded-xl bg-blue-700 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-700/20 transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
                {{ __('Log in') }}
            </button>

            {{-- Register Link --}}
            <p class="pt-2 text-center text-sm text-gray-600">
                Don’t have an account?

                <a href="{{ route('register') }}" class="font-semibold text-blue-700 hover:text-blue-900">
                    Create account
                </a>
            </p>
        </form>
    </div>
</x-guest-layout>