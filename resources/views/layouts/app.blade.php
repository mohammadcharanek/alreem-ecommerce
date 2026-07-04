<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>@yield('title', 'Alreem')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO: defaults can be overridden per-view with @section(...) --}}
    <meta name="description" content="@yield('meta_description', 'Deluxe Plus — premium dental & medical supplies. Shop quality brands with great service and fast shipping.')">
    <meta name="robots" content="@yield('robots', 'index,follow')">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    {{-- Open Graph / Twitter --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="Deluxe Plus">
    <meta property="og:title" content="@yield('og_title', trim($__env->yieldContent('title', 'Deluxe Plus')))">
    <meta property="og:description" content="@yield('og_description', trim($__env->yieldContent('meta_description', 'Deluxe Plus — premium dental & medical supplies.')))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))">
    <meta name="twitter:card" content="summary_large_image">

    {{-- JSON-LD blocks from pages --}}
    @stack('structured-data')

    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Swiper CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}" />
</head>

<body class="flex flex-col min-h-screen bg-gray-100 text-gray-800">
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
    @endphp

    {{-- Navbar --}}
    <nav x-data="{ open: false }" class="bg-white border-b shadow sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-14 sm:h-16">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2 min-w-0 shrink-0">
                    <img src="{{ asset('storage/logo.jpeg') }}" alt="Logo" class="h-8 w-8 sm:h-10 sm:w-10 rounded object-cover shrink-0" />
                    <span class="text-lg sm:text-2xl font-extrabold tracking-wide text-brand-blue truncate">
                        Alreem
                    </span>
                </a>

                {{-- Desktop links --}}
                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('home') }}" class="hover:text-green-600">Home</a>
                    <a href="{{ route('products.index') }}" class="hover:text-green-600">Products</a>
                    <a href="{{ route('contact.show') }}" class="hover:text-green-600">Contact</a>

                    @auth
                        @if(auth()->user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:underline">⚙️ Admin Panel</a>
                        @endif

                        <a href="{{ route('wishlist.index') }}" class="relative inline-flex items-center gap-1 hover:text-pink-600">
                            <span class="relative inline-flex items-center justify-center h-6 w-6">
                                ❤️
                                <span id="wishlist-count"
                                      data-count="{{ $wishlistCount }}"
                                      class="absolute -top-2 -right-2 z-10 min-w-[18px] h-[18px] px-1 items-center justify-center bg-red-600 text-white text-[10px] leading-none rounded-full"
                                      style="display: {{ $wishlistCount > 0 ? 'flex' : 'none' }};">
                                    {{ $wishlistCount }}
                                </span>
                            </span>
                            <span>Wishlist</span>
                        </a>

                        <span class="text-gray-700">Hi, {{ Auth::user()->name }}</span>

                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-red-600 hover:underline">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600">Sign In</a>
                        <a href="{{ route('register') }}" class="text-gray-700 hover:text-blue-600">Sign Up</a>
                    @endauth

                    <a href="{{ route('orders.index') }}" class="text-gray-700 hover:text-blue-600">Orders</a>

                    <a href="{{ route('cart.index') }}" class="relative inline-flex items-center justify-center h-6 w-6 text-indigo-600 hover:text-indigo-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.936-4.706 2.436-7.184.121-.598-.135-1.184-.634-1.545-.5-.361-1.155-.361-1.655-.037L7.5 14.25zm0 0L5.106 5.272M6 18.75a1.5 1.5 0 100 3 1.5 1.5 0 000-3zm12 0a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" />
                        </svg>

                        <span id="cart-count"
                              data-count="{{ $cartCount }}"
                              class="absolute -top-2 -right-2 z-10 min-w-[18px] h-[18px] px-1 items-center justify-center bg-red-500 text-white text-[10px] leading-none rounded-full"
                              style="display: {{ $cartCount > 0 ? 'flex' : 'none' }};">
                            {{ $cartCount }}
                        </span>
                    </a>
                </div>

                {{-- Mobile cart icon + hamburger --}}
                <div class="md:hidden flex items-center gap-1 shrink-0">
                    <a href="{{ route('cart.index') }}" class="relative inline-flex items-center justify-center h-11 w-11 text-indigo-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.936-4.706 2.436-7.184.121-.598-.135-1.184-.634-1.545-.5-.361-1.155-.361-1.655-.037L7.5 14.25zm0 0L5.106 5.272M6 18.75a1.5 1.5 0 100 3 1.5 1.5 0 000-3zm12 0a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" />
                        </svg>

                        <span id="cart-count-mobile-icon"
                              data-count="{{ $cartCount }}"
                              class="absolute top-0.5 right-0.5 z-10 min-w-[16px] h-[16px] px-1 items-center justify-center bg-red-500 text-white text-[10px] leading-none rounded-full"
                              style="display: {{ $cartCount > 0 ? 'flex' : 'none' }};">
                            {{ $cartCount }}
                        </span>
                    </a>

                    <button @click="open = !open"
                            class="inline-flex items-center justify-center h-11 w-11 text-gray-600 focus:outline-none"
                            aria-label="Toggle menu">
                        <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16" />
                        </svg>

                        <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="open"
             x-transition
             class="md:hidden border-t bg-white"
             @click.away="open = false"
             @click="open = false"
             style="display: none;">
            <div class="px-4 py-3 space-y-1">
                <a href="{{ route('home') }}" class="flex items-center h-11 hover:text-green-600">Home</a>
                <a href="{{ route('products.index') }}" class="flex items-center h-11 hover:text-green-600">Products</a>
                <a href="{{ route('contact.show') }}" class="flex items-center h-11 hover:text-green-600">Contact</a>
                <a href="{{ route('orders.index') }}" class="flex items-center h-11 text-gray-700 hover:text-blue-600">Orders</a>

                @auth
                    @if(auth()->user()->is_admin)
                        <div class="border-t my-1"></div>
                        <a href="{{ route('admin.products.create') }}" class="flex items-center h-11 text-blue-600 hover:underline">➕ Add Product</a>
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center h-11 text-blue-600 hover:underline">⚙️ Admin Panel</a>
                    @endif

                    <div class="border-t my-1"></div>

                    <a href="{{ route('wishlist.index') }}" class="flex items-center gap-2 h-11 hover:text-pink-600">
                        <span class="relative inline-flex items-center justify-center h-5 w-5">❤️</span>
                        <span>Wishlist</span>
                        <span id="wishlist-count-mobile"
                              data-count="{{ $wishlistCount }}"
                              class="min-w-[18px] h-[18px] px-1 items-center justify-center bg-red-600 text-white text-[10px] leading-none rounded-full"
                              style="display: {{ $wishlistCount > 0 ? 'flex' : 'none' }};">
                            {{ $wishlistCount }}
                        </span>
                    </a>

                    <div class="flex items-center justify-between h-11">
                        <span class="text-gray-700">Hi, {{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-red-600 hover:underline">Logout</button>
                        </form>
                    </div>
                @else
                    <div class="border-t my-1"></div>
                    <a href="{{ route('login') }}" class="flex items-center h-11 text-gray-700 hover:text-blue-600">Sign In</a>
                    <a href="{{ route('register') }}" class="flex items-center h-11 text-gray-700 hover:text-blue-600">Sign Up</a>
                @endauth
            </div>
        </div>
    </nav>

    @include('partials.categories-bar')

    {{-- Main Content --}}
    <main class="flex-grow">
        <div class="max-w-7xl mx-auto px-4 py-8">
            @yield('content')
        </div>
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-gray-300 mt-16">
        <div class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <img src="{{ asset('storage/logo.jpeg') }}" alt="Alreem Logo" class="w-32 mb-4">
                <p class="text-gray-400 text-sm">
                    Al Reem Expo specializes in supplying premium imported products from around the world.
                </p>
            </div>

            <div>
                <h3 class="text-white font-semibold mb-4">Quick Links</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-white">Home</a></li>
                    <li><a href="{{ route('products.index') }}" class="hover:text-white">Shop</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-white">About Us</a></li>
                    <li><a href="{{ route('contact.show') }}" class="hover:text-white">Contact</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-semibold mb-4">Customer Service</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ url('/faq') }}" class="hover:text-white">FAQ</a></li>
                    <li><a href="{{ url('/returns') }}" class="hover:text-white">Returns</a></li>
                    <li><a href="{{ url('/shipping') }}" class="hover:text-white">Shipping</a></li>
                    <li><a href="{{ url('/support') }}" class="hover:text-white">Support</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-semibold mb-4">Newsletter</h3>
                <p class="text-gray-400 text-sm mb-4">Subscribe to get the latest updates and offers.</p>

                @if(session('success'))
                    <div class="bg-green-100 text-green-800 p-2 rounded mb-3">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="flex">
                    @csrf
                    <input type="email" name="email" placeholder="Enter your email" required
                           class="w-full px-3 py-2 rounded-l bg-gray-800 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-r text-white font-semibold transition">
                        Subscribe
                    </button>
                </form>

                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="border-t border-gray-800 mt-8 py-4 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} Al Reem Expo. Developed by Web Weavers Software All rights reserved.
        </div>
    </footer>

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