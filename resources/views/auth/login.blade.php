<x-guest-layout>
    <div class="mx-auto w-full max-w-md">
        {{-- Header --}}
        <div class="mb-8 text-center sm:text-left">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900">
                Welcome back
            </h2>

            <p class="mt-2 text-sm leading-6 text-gray-600">
                Log in to continue shopping, manage your cart, and track your orders.
            </p>
        </div>

        {{-- Session Status --}}
        <x-auth-session-status
            class="mb-5 rounded-xl bg-green-50 p-4 text-sm text-green-700 ring-1 ring-green-100"
            :status="session('status')"
        />

        {{-- Google Authentication Error --}}
        @if ($errors->has('google'))
            <div
                class="mb-5 rounded-xl bg-red-50 p-4 text-sm text-red-700 ring-1 ring-red-100"
                role="alert"
            >
                {{ $errors->first('google') }}
            </div>
        @endif

        {{-- Google Login --}}
        @if (Route::has('google.redirect'))
            <a
                href="{{ route('google.redirect') }}"
                class="flex w-full items-center justify-center gap-3 rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 shadow-sm transition hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
                {{-- Google Logo --}}
                <svg
                    class="h-5 w-5 shrink-0"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path
                        fill="#4285F4"
                        d="M21.6 12.227c0-.709-.064-1.391-.182-2.045H12v3.873h5.382a4.6 4.6 0 0 1-1.995 3.018v2.509h3.227c1.891-1.741 2.986-4.305 2.986-7.355Z"
                    />
                    <path
                        fill="#34A853"
                        d="M12 22c2.7 0 4.968-.895 6.614-2.418l-3.227-2.509c-.895.6-2.041.955-3.387.955-2.605 0-4.809-1.759-5.6-4.123H3.064v2.591A9.996 9.996 0 0 0 12 22Z"
                    />
                    <path
                        fill="#FBBC05"
                        d="M6.4 13.905A6.017 6.017 0 0 1 6.086 12c0-.664.114-1.309.314-1.905V7.504H3.064A9.995 9.995 0 0 0 2 12c0 1.614.386 3.145 1.064 4.496L6.4 13.905Z"
                    />
                    <path
                        fill="#EA4335"
                        d="M12 5.973c1.468 0 2.786.505 3.823 1.495l2.864-2.864C16.964 2.995 14.7 2 12 2a9.996 9.996 0 0 0-8.936 5.504L6.4 10.095c.791-2.364 2.995-4.122 5.6-4.122Z"
                    />
                </svg>

                <span>
                    Continue with Google
                </span>
            </a>

            {{-- Divider --}}
            <div class="my-6 flex items-center gap-4">
                <div class="h-px flex-1 bg-gray-200"></div>

                <span class="text-xs font-medium uppercase tracking-wider text-gray-400">
                    Or continue with email
                </span>

                <div class="h-px flex-1 bg-gray-200"></div>
            </div>
        @endif

        {{-- Email and Password Login --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            {{-- Email Address --}}
            <div>
                <x-input-label
                    for="email"
                    :value="__('Email Address')"
                    class="text-sm font-semibold text-gray-700"
                />

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

                <x-input-error
                    :messages="$errors->get('email')"
                    class="mt-2"
                />
            </div>

            {{-- Password --}}
            <div>
                <div class="flex items-center justify-between gap-4">
                    <x-input-label
                        for="password"
                        :value="__('Password')"
                        class="text-sm font-semibold text-gray-700"
                    />

                    @if (Route::has('password.request'))
                        <a
                            href="{{ route('password.request') }}"
                            class="text-sm font-medium text-blue-700 transition hover:text-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            Forgot password?
                        </a>
                    @endif
                </div>

                <div class="relative mt-2">
                    <x-text-input
                        id="password"
                        class="block w-full rounded-xl border-gray-300 px-4 py-3 pr-12 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Enter your password"
                    />

                    {{-- Show/Hide Password Button --}}
                    <button
                        type="button"
                        id="toggle-password"
                        class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 transition hover:text-gray-700 focus:outline-none"
                        aria-label="Show password"
                        aria-controls="password"
                        aria-pressed="false"
                    >
                        {{-- Eye Open --}}
                        <svg
                            id="eye-open-icon"
                            class="h-5 w-5"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.8"
                            stroke="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                            />
                        </svg>

                        {{-- Eye Closed --}}
                        <svg
                            id="eye-closed-icon"
                            class="hidden h-5 w-5"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.8"
                            stroke="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3.98 8.223A10.477 10.477 0 0 0 2.036 11.68a1.017 1.017 0 0 0 0 .639C3.423 16.49 7.36 19.5 12 19.5c1.757 0 3.412-.432 4.865-1.194M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639a10.47 10.47 0 0 1-2.293 3.95M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"
                            />
                        </svg>
                    </button>
                </div>

                <x-input-error
                    :messages="$errors->get('password')"
                    class="mt-2"
                />
            </div>

            {{-- Remember Me --}}
            <div class="flex items-center">
                <label
                    for="remember_me"
                    class="inline-flex cursor-pointer items-center"
                >
                    <input
                        id="remember_me"
                        type="checkbox"
                        name="remember"
                        class="rounded border-gray-300 text-blue-700 shadow-sm focus:ring-blue-600"
                        @checked(old('remember'))
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
            @if (Route::has('register'))
                <p class="pt-2 text-center text-sm text-gray-600">
                    Don’t have an account?

                    <a
                        href="{{ route('register') }}"
                        class="font-semibold text-blue-700 transition hover:text-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Create account
                    </a>
                </p>
            @endif
        </form>
    </div>

    {{-- Password Visibility --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.getElementById('toggle-password');
            const eyeOpenIcon = document.getElementById('eye-open-icon');
            const eyeClosedIcon = document.getElementById('eye-closed-icon');

            if (
                !passwordInput ||
                !toggleButton ||
                !eyeOpenIcon ||
                !eyeClosedIcon
            ) {
                return;
            }

            toggleButton.addEventListener('click', function () {
                const passwordIsHidden = passwordInput.type === 'password';

                passwordInput.type = passwordIsHidden ? 'text' : 'password';

                eyeOpenIcon.classList.toggle('hidden', passwordIsHidden);
                eyeClosedIcon.classList.toggle('hidden', !passwordIsHidden);

                toggleButton.setAttribute(
                    'aria-label',
                    passwordIsHidden ? 'Hide password' : 'Show password'
                );

                toggleButton.setAttribute(
                    'aria-pressed',
                    passwordIsHidden ? 'true' : 'false'
                );
            });
        });
    </script>
</x-guest-layout>