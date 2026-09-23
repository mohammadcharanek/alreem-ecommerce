<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>@yield('title', 'Al Reem Expo')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO: defaults can be overridden per-view with @section(...) --}}
    <meta name="description" content="@yield('meta_description', 'Al Reem Expo specializes in supplying premium imported products from around the world.')">
    <meta name="robots" content="@yield('robots', 'index,follow')">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    {{-- Open Graph / Twitter --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="Al Reem Expo">
    <meta property="og:title" content="@yield('og_title', trim($__env->yieldContent('title', 'Al Reem Expo')))">
    <meta property="og:description" content="@yield('og_description', trim($__env->yieldContent('meta_description', 'Al Reem Expo specializes in supplying premium imported products from around the world.')))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))">
    <meta name="twitter:card" content="summary_large_image">

    {{-- JSON-LD blocks from pages --}}
    @stack('structured-data')

    <style>
        [x-cloak] { display: none !important; }

        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Swiper CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}" />
</head>

<body class="flex min-h-screen flex-col bg-slate-50 text-slate-800 antialiased">
    @php
        $cartCount = 0;

        try {
            if (Auth::check()) {
                $cart = \App\Models\Cart::firstOrCreate(
                    ['user_id' => Auth::id(), 'status' => 'active'],
                    ['total_amount' => 0]
                );

                $cartCount = (int) $cart->items()->sum('quantity');
            } else {
                $sessionCart = session()->get('cart', []);
                $cartCount = is_array($sessionCart) ? (int) array_sum($sessionCart) : 0;
            }
        } catch (\Throwable $e) {
            $cartCount = 0;
            report($e);
        }

        $wishlistCount = 0;

        if (Auth::check()) {
            try {
                $wishlistCount = (int) (auth()->user()->wishlist?->count() ?? 0);
            } catch (\Throwable $e) {
                $wishlistCount = 0;
                report($e);
            }
        }

        $isProductsArea = request()->routeIs(
            'products.index',
            'products.show',
            'products.byCategory'
        );
    @endphp

    {{-- ========================================================= --}}
    {{-- PRIMARY HEADER --}}
    {{-- ========================================================= --}}
    <header
        x-data="{ open: false, accountOpen: false }"
        class="sticky top-0 z-[70] border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur-xl"
    >
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-[72px] items-center justify-between gap-5">

                {{-- Brand --}}
                <a
                    href="{{ route('home') }}"
                    class="group flex min-w-0 shrink-0 items-center gap-3 rounded-xl focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-blue focus-visible:ring-offset-2"
                    aria-label="Al Reem Expo home"
                >
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-white">
                        <img
                            src="{{ asset('storage/logo.jpeg') }}"
                            alt="Al Reem Expo"
                            class="h-full w-full object-contain"
                        >
                    </span>

                    <span class="min-w-0 leading-none">
                        <span class="block truncate text-xl font-black tracking-tight text-slate-950 transition group-hover:text-brand-blue sm:text-2xl">
                            Al Reem
                        </span>
                        <span class="mt-1 hidden text-[9px] font-bold uppercase tracking-[0.28em] text-slate-500 sm:block">
                            Expo
                        </span>
                    </span>
                </a>

                {{-- Desktop navigation --}}
                <nav class="hidden items-center gap-1 lg:flex" aria-label="Primary navigation">
                    <a
                        href="{{ route('home') }}"
                        class="rounded-xl px-4 py-2.5 text-sm font-semibold transition
                               {{ request()->routeIs('home')
                                    ? 'bg-blue-50 text-blue-700'
                                    : 'text-slate-700 hover:bg-slate-50 hover:text-blue-700' }}"
                    >
                        Home
                    </a>

                    <a
                        href="{{ route('products.index') }}"
                        class="rounded-xl px-4 py-2.5 text-sm font-semibold transition
                               {{ $isProductsArea
                                    ? 'bg-blue-50 text-blue-700'
                                    : 'text-slate-700 hover:bg-slate-50 hover:text-blue-700' }}"
                    >
                        Products
                    </a>

                    <a
                        href="{{ route('contact.show') }}"
                        class="rounded-xl px-4 py-2.5 text-sm font-semibold transition
                               {{ request()->routeIs('contact.*')
                                    ? 'bg-blue-50 text-blue-700'
                                    : 'text-slate-700 hover:bg-slate-50 hover:text-blue-700' }}"
                    >
                        Contact
                    </a>
                </nav>

                {{-- Desktop actions --}}
                <div class="hidden items-center gap-2 lg:flex">
                    <a
                        href="{{ route('orders.index') }}"
                        class="inline-flex min-h-11 items-center gap-2 rounded-xl px-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-blue-700"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M6 3h12l2 4v13H4V7l2-4Z"></path>
                            <path d="M4 8h16M9 12h6"></path>
                        </svg>
                        Orders
                    </a>

                    @auth
                        <a
                            href="{{ route('wishlist.index') }}"
                            class="relative inline-flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600"
                            aria-label="Wishlist"
                            title="Wishlist"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8Z"></path>
                            </svg>

                            <span
                                id="wishlist-count"
                                data-count="{{ $wishlistCount }}"
                                class="absolute -right-1.5 -top-1.5 min-w-[18px] h-[18px] px-1 items-center justify-center rounded-full bg-rose-600 text-[10px] font-bold leading-none text-white"
                                style="display: {{ $wishlistCount > 0 ? 'flex' : 'none' }};"
                            >
                                {{ $wishlistCount }}
                            </span>
                        </a>
                    @endauth

                    <a
                        href="{{ route('cart.index') }}"
                        class="relative inline-flex h-11 w-11 items-center justify-center rounded-xl bg-slate-950 text-white shadow-sm transition hover:bg-blue-700"
                        aria-label="Shopping cart"
                        title="Shopping cart"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                            <path d="M3 4h2l2.3 10.1a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 1.9-1.5L21 7H6"></path>
                            <circle cx="10" cy="20" r="1"></circle>
                            <circle cx="18" cy="20" r="1"></circle>
                        </svg>

                        <span
                            id="cart-count"
                            data-count="{{ $cartCount }}"
                            class="absolute -right-1.5 -top-1.5 min-w-[18px] h-[18px] px-1 items-center justify-center rounded-full bg-blue-600 text-[10px] font-bold leading-none text-white ring-2 ring-white"
                            style="display: {{ $cartCount > 0 ? 'flex' : 'none' }};"
                        >
                            {{ $cartCount }}
                        </span>
                    </a>

                    @auth
                        <div class="relative">
                            <button
                                type="button"
                                @click="accountOpen = !accountOpen"
                                @keydown.escape.window="accountOpen = false"
                                :aria-expanded="accountOpen.toString()"
                                class="inline-flex min-h-11 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
                            >
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-50 text-blue-700">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                        <circle cx="12" cy="8" r="4"></circle>
                                        <path d="M4 21a8 8 0 0 1 16 0"></path>
                                    </svg>
                                </span>

                                <span class="max-w-[9rem] truncate">
                                    {{ Auth::user()->name }}
                                </span>

                                <svg class="h-4 w-4 transition" :class="{ 'rotate-180': accountOpen }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.128l3.71-3.9a.75.75 0 1 1 1.08 1.04l-4.24 4.46a.75.75 0 0 1-1.08 0l-4.24-4.46a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"></path>
                                </svg>
                            </button>

                            <div
                                x-show="accountOpen"
                                x-cloak
                                x-transition.origin.top.right
                                @click.outside="accountOpen = false"
                                class="absolute right-0 top-full mt-2 w-60 overflow-hidden rounded-2xl border border-slate-200 bg-white p-2 shadow-xl"
                            >
                                <div class="border-b border-slate-100 px-3 py-3">
                                    <p class="truncate text-sm font-bold text-slate-900">
                                        {{ Auth::user()->name }}
                                    </p>
                                    <p class="mt-1 truncate text-xs text-slate-500">
                                        {{ Auth::user()->email }}
                                    </p>
                                </div>

                                @if(auth()->user()->is_admin)
                                    <a href="{{ route('admin.dashboard') }}" class="mt-2 flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-semibold text-blue-700 transition hover:bg-blue-50">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path d="M12 3 4 7v5c0 5 3.5 8 8 9 4.5-1 8-4 8-9V7l-8-4Z"></path>
                                            <path d="M9 12h6M12 9v6"></path>
                                        </svg>
                                        Admin Panel
                                    </a>
                                @endif

                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm text-slate-700 transition hover:bg-slate-50">
                                    Profile
                                </a>

                                <a href="{{ route('wishlist.index') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm text-slate-700 transition hover:bg-slate-50">
                                    Wishlist
                                </a>

                                <a href="{{ route('orders.index') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm text-slate-700 transition hover:bg-slate-50">
                                    Orders
                                </a>

                                <form method="POST" action="{{ route('logout') }}" class="mt-1 border-t border-slate-100 pt-1">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center rounded-xl px-3 py-2.5 text-left text-sm font-semibold text-rose-600 transition hover:bg-rose-50">
                                        Sign out
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex min-h-11 items-center justify-center rounded-xl px-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-blue-700"
                        >
                            Sign in
                        </a>

                        <a
                            href="{{ route('register') }}"
                            class="inline-flex min-h-11 items-center justify-center rounded-xl bg-blue-700 px-4 text-sm font-bold text-white shadow-sm transition hover:bg-blue-800"
                        >
                            Create account
                        </a>
                    @endauth
                </div>

                {{-- Mobile actions --}}
                <div class="flex items-center gap-1 lg:hidden">
                    <a
                        href="{{ route('cart.index') }}"
                        class="relative inline-flex h-11 w-11 items-center justify-center rounded-xl text-slate-700 transition hover:bg-slate-100"
                        aria-label="Shopping cart"
                    >
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                            <path d="M3 4h2l2.3 10.1a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 1.9-1.5L21 7H6"></path>
                            <circle cx="10" cy="20" r="1"></circle>
                            <circle cx="18" cy="20" r="1"></circle>
                        </svg>

                        <span
                            id="cart-count-mobile-icon"
                            data-count="{{ $cartCount }}"
                            class="absolute right-0 top-0 min-w-[17px] h-[17px] px-1 items-center justify-center rounded-full bg-blue-600 text-[9px] font-bold leading-none text-white ring-2 ring-white"
                            style="display: {{ $cartCount > 0 ? 'flex' : 'none' }};"
                        >
                            {{ $cartCount }}
                        </span>
                    </a>

                    <button
                        type="button"
                        @click="open = !open"
                        @keydown.escape.window="open = false"
                        class="inline-flex h-11 w-11 items-center justify-center rounded-xl text-slate-700 transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        :aria-expanded="open.toString()"
                        aria-label="Toggle navigation menu"
                    >
                        <svg x-show="!open" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                            <path d="M4 7h16M4 12h16M4 17h16"></path>
                        </svg>

                        <svg x-show="open" x-cloak class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                            <path d="m6 6 12 12M18 6 6 18"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div
            x-show="open"
            x-cloak
            x-transition
            @click.outside="open = false"
            class="border-t border-slate-200 bg-white lg:hidden"
        >
            <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6">
                <nav class="grid gap-1" aria-label="Mobile navigation">
                    <a href="{{ route('home') }}" class="flex min-h-11 items-center rounded-xl px-3 text-sm font-semibold {{ request()->routeIs('home') ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-50' }}">
                        Home
                    </a>

                    <a href="{{ route('products.index') }}" class="flex min-h-11 items-center rounded-xl px-3 text-sm font-semibold {{ $isProductsArea ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-50' }}">
                        Products
                    </a>

                    <a href="{{ route('contact.show') }}" class="flex min-h-11 items-center rounded-xl px-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Contact
                    </a>

                    <a href="{{ route('orders.index') }}" class="flex min-h-11 items-center justify-between rounded-xl px-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        <span>Orders</span>
                    </a>

                    <a href="{{ route('cart.index') }}" class="flex min-h-11 items-center justify-between rounded-xl px-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        <span>Cart</span>
                        <span
                            id="cart-count-mobile"
                            data-count="{{ $cartCount }}"
                            class="min-w-[20px] h-5 px-1.5 items-center justify-center rounded-full bg-blue-600 text-[10px] font-bold text-white"
                            style="display: {{ $cartCount > 0 ? 'flex' : 'none' }};"
                        >
                            {{ $cartCount }}
                        </span>
                    </a>

                    @auth
                        <a href="{{ route('wishlist.index') }}" class="flex min-h-11 items-center justify-between rounded-xl px-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            <span>Wishlist</span>
                            <span
                                id="wishlist-count-mobile"
                                data-count="{{ $wishlistCount }}"
                                class="min-w-[20px] h-5 px-1.5 items-center justify-center rounded-full bg-rose-600 text-[10px] font-bold text-white"
                                style="display: {{ $wishlistCount > 0 ? 'flex' : 'none' }};"
                            >
                                {{ $wishlistCount }}
                            </span>
                        </a>

                        <a href="{{ route('profile.edit') }}" class="flex min-h-11 items-center rounded-xl px-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Profile
                        </a>

                        @if(auth()->user()->is_admin)
                            <div class="my-2 border-t border-slate-100"></div>

                            <a href="{{ route('admin.products.create') }}" class="flex min-h-11 items-center rounded-xl px-3 text-sm font-semibold text-blue-700 hover:bg-blue-50">
                                Add Product
                            </a>

                            <a href="{{ route('admin.dashboard') }}" class="flex min-h-11 items-center rounded-xl px-3 text-sm font-semibold text-blue-700 hover:bg-blue-50">
                                Admin Panel
                            </a>
                        @endif

                        <div class="my-2 border-t border-slate-100"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex min-h-11 w-full items-center rounded-xl px-3 text-left text-sm font-semibold text-rose-600 hover:bg-rose-50">
                                Sign out
                            </button>
                        </form>
                    @else
                        <div class="my-2 border-t border-slate-100"></div>

                        <div class="grid grid-cols-2 gap-2">
                            <a href="{{ route('login') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200 text-sm font-semibold text-slate-700">
                                Sign in
                            </a>

                            <a href="{{ route('register') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-blue-700 text-sm font-bold text-white">
                                Create account
                            </a>
                        </div>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    @include('partials.categories-bar')

    {{-- ========================================================= --}}
    {{-- MAIN CONTENT --}}
    {{-- ========================================================= --}}
    <main class="flex-grow">
        <div class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    {{-- ========================================================= --}}
    {{-- FOOTER --}}
    {{-- ========================================================= --}}
    <footer class="mt-16 bg-gray-900 text-gray-300">
        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-6 py-12 md:grid-cols-4">
            <div>
                <img src="{{ asset('storage/logo.jpeg') }}" alt="Al Reem Expo Logo" class="mb-4 w-32">
                <p class="text-sm text-gray-400">
                    Al Reem Expo specializes in supplying premium imported products from around the world.
                </p>
            </div>

            <div>
                <h3 class="mb-4 font-semibold text-white">Quick Links</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-white">Home</a></li>
                    <li><a href="{{ route('products.index') }}" class="hover:text-white">Shop</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-white">About Us</a></li>
                    <li><a href="{{ route('contact.show') }}" class="hover:text-white">Contact</a></li>
                </ul>
            </div>

            <div>
                <h3 class="mb-4 font-semibold text-white">Customer Service</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ url('/faq') }}" class="hover:text-white">FAQ</a></li>
                    <li><a href="{{ url('/returns') }}" class="hover:text-white">Returns</a></li>
                    <li><a href="{{ url('/shipping') }}" class="hover:text-white">Shipping</a></li>
                    <li><a href="{{ url('/support') }}" class="hover:text-white">Support</a></li>
                </ul>
            </div>

            <div>
                <h3 class="mb-4 font-semibold text-white">Newsletter</h3>
                <p class="mb-4 text-sm text-gray-400">Subscribe to get the latest updates and offers.</p>

                @if(session('success'))
                    <div class="mb-3 rounded bg-green-100 p-2 text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="flex">
                    @csrf
                    <input
                        type="email"
                        name="email"
                        placeholder="Enter your email"
                        required
                        class="w-full rounded-l bg-gray-800 px-3 py-2 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-600"
                    >
                    <button
                        type="submit"
                        class="rounded-r bg-blue-600 px-4 py-2 font-semibold text-white transition hover:bg-blue-700"
                    >
                        Subscribe
                    </button>
                </form>

                @error('email')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="border-t border-gray-800 py-4 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} Al Reem Expo. Developed by Web Weavers Software. All rights reserved.
        </div>
    </footer>

    {{-- ========================================================= --}}
    {{-- FLOATING WHATSAPP --}}
    {{-- ========================================================= --}}
    <a
        href="https://wa.me/96181161440?text={{ urlencode('Hello Al Reem Expo, I need help with a product.') }}"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="Chat with Al Reem Expo on WhatsApp"
        title="Chat with us on WhatsApp"
        class="fixed bottom-5 right-5 z-[80] inline-flex h-14 w-14 items-center justify-center rounded-full bg-emerald-500 text-white shadow-xl ring-4 ring-white/90 transition duration-200 hover:-translate-y-0.5 hover:bg-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-200 sm:h-16 sm:w-16"
    >
        <svg class="h-7 w-7 sm:h-8 sm:w-8" viewBox="0 0 32 32" fill="currentColor" aria-hidden="true">
            <path d="M16.04 2.003A13.9 13.9 0 0 0 4.11 23.06L2 30l7.12-2.03a13.97 13.97 0 1 0 6.92-25.967Zm0 25.45a11.45 11.45 0 0 1-5.84-1.6l-.42-.25-4.23 1.21 1.13-4.12-.27-.43a11.45 11.45 0 1 1 9.63 5.19Zm6.28-8.57c-.34-.17-2.03-1-2.35-1.11-.31-.11-.54-.17-.77.17-.23.34-.89 1.11-1.09 1.34-.2.23-.4.26-.74.09-.34-.17-1.44-.53-2.74-1.69-1.01-.9-1.7-2.02-1.9-2.36-.2-.34-.02-.52.15-.69.15-.15.34-.4.51-.6.17-.2.23-.34.34-.57.11-.23.06-.43-.03-.6-.09-.17-.77-1.86-1.06-2.55-.28-.67-.56-.58-.77-.59h-.66c-.23 0-.6.09-.91.43-.31.34-1.2 1.17-1.2 2.86s1.23 3.32 1.4 3.55c.17.23 2.42 3.69 5.86 5.17.82.35 1.46.56 1.96.72.82.26 1.57.22 2.16.13.66-.1 2.03-.83 2.32-1.63.29-.8.29-1.49.2-1.63-.09-.14-.31-.23-.65-.4Z"/>
        </svg>

        <span class="sr-only">Chat on WhatsApp</span>
    </a>

    {{-- Swiper JS --}}
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.swiper').forEach(swiperEl => {
                new Swiper(swiperEl, {
                    slidesPerView: 1.3,
                    spaceBetween: 16,
                    loop: true,
                    grabCursor: true,
                    navigation: {
                        nextEl: swiperEl.querySelector('.swiper-button-next'),
                        prevEl: swiperEl.querySelector('.swiper-button-prev'),
                    },
                    breakpoints: {
                        640: { slidesPerView: 2.3 },
                        1024: { slidesPerView: 4 },
                    },
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
