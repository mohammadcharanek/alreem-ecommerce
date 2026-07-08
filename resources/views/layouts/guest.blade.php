<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Al Reem Expo') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-slate-100 flex items-center justify-center px-4 py-8 sm:px-6 lg:px-8">

        <div class="w-full max-w-6xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-gray-100 lg:grid lg:grid-cols-2">

            {{-- Left Branding Panel --}}
            <div class="hidden lg:flex flex-col justify-between bg-gradient-to-br from-blue-700 via-blue-800 to-blue-950 p-10 text-white">
                <div>
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white p-2 shadow-lg">
                            <x-application-logo class="h-12 w-12 fill-current text-blue-800" />
                        </div>

                        <div>
                            <h1 class="text-2xl font-bold tracking-tight">
                                Al Reem Expo
                            </h1>
                            <p class="mt-1 text-sm text-blue-100">
                                Global Import & Export
                            </p>
                        </div>
                    </a>
                </div>

                <div class="max-w-md">
                    <p class="mb-4 inline-flex rounded-full bg-white/10 px-4 py-2 text-sm font-medium text-blue-100 ring-1 ring-white/20">
                        Secure customer access
                    </p>

                    <h2 class="text-4xl font-bold leading-tight">
                        Manage your orders with confidence.
                    </h2>

                    <p class="mt-5 text-lg leading-8 text-blue-100">
                        Sign in to continue shopping, manage your cart, save wishlist items, and complete checkout faster.
                    </p>
                </div>

                <div class="grid grid-cols-3 gap-4 text-sm text-blue-100">
                    <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                        <div class="font-semibold text-white">Fast</div>
                        <div class="mt-1">Checkout</div>
                    </div>

                    <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                        <div class="font-semibold text-white">Secure</div>
                        <div class="mt-1">Account</div>
                    </div>

                    <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                        <div class="font-semibold text-white">Easy</div>
                        <div class="mt-1">Orders</div>
                    </div>
                </div>
            </div>

            {{-- Right Form Panel --}}
            <div class="w-full px-5 py-8 sm:px-8 sm:py-10 lg:px-12">
                {{-- Mobile Logo --}}
                <div class="mb-8 text-center lg:hidden">
                    <a href="{{ url('/') }}" class="inline-flex flex-col items-center">
                        <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-blue-50 p-3 shadow-sm ring-1 ring-blue-100">
                            <x-application-logo class="h-14 w-14 fill-current text-blue-800" />
                        </div>

                        <span class="mt-4 text-2xl font-bold text-blue-800">
                            Al Reem Expo
                        </span>

                        <span class="mt-1 text-sm text-gray-500">
                            Global Import & Export
                        </span>
                    </a>
                </div>

                {{ $slot }}
            </div>
        </div>

    </div>
</body>
</html>