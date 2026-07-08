<x-guest-layout>
    <div class="mx-auto w-full max-w-md">
        {{-- Header --}}
        <div class="mb-8">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900">
                Create your account
            </h2>

            <p class="mt-2 text-sm leading-6 text-gray-600">
                Register to order faster, save your wishlist, and complete checkout easily.
            </p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            {{-- Name --}}
            <div>
                <x-input-label for="name" :value="__('Full Name')" class="text-sm font-semibold text-gray-700" />

                <x-text-input
                    id="name"
                    class="mt-2 block w-full rounded-xl border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    type="text"
                    name="name"
                    :value="old('name')"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Your full name"
                />

                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

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
                    autocomplete="username"
                    placeholder="you@example.com"
                />

                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            {{-- Phone --}}
            <div>
                <x-input-label for="phone" :value="__('Phone Number')" class="text-sm font-semibold text-gray-700" />

                <x-text-input
                    id="phone"
                    class="mt-2 block w-full rounded-xl border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    type="text"
                    name="phone"
                    :value="old('phone')"
                    required
                    autocomplete="tel"
                    placeholder="03 123 456"
                />

                <p class="mt-1 text-xs text-gray-500">
                    Used for order confirmation and delivery contact.
                </p>

                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            {{-- Password --}}
            <div>
                <x-input-label for="password" :value="__('Password')" class="text-sm font-semibold text-gray-700" />

                <x-text-input
                    id="password"
                    class="mt-2 block w-full rounded-xl border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    placeholder="Create a password"
                />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            {{-- Confirm Password --}}
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-sm font-semibold text-gray-700" />

                <x-text-input
                    id="password_confirmation"
                    class="mt-2 block w-full rounded-xl border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Repeat your password"
                />

                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            {{-- Submit --}}
            <button
                type="submit"
                class="flex w-full items-center justify-center rounded-xl bg-blue-700 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-700/20 transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
                {{ __('Create Account') }}
            </button>

            {{-- Login Link --}}
            <p class="pt-2 text-center text-sm text-gray-600">
                Already registered?

                <a href="{{ route('login') }}" class="font-semibold text-blue-700 hover:text-blue-900">
                    Log in
                </a>
            </p>
        </form>
    </div>
</x-guest-layout>