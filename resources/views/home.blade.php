@extends('layouts.app')

@section('title', 'Al Reem Expo | Imported Products & Delivery in Lebanon')
@section(
    'meta_description',
    'Al Reem Expo imports selected food, beverage, personal care, and household products from the USA, Canada, and Brazil for customers in Lebanon.'
)
@section('canonical', route('home'))
@section('robots', 'index,follow')

@section('content')

@php
    $homeSlides = [
        [
            'desktop' => asset('images/Slider1IMG.jpg'),
            'mobile'  => asset('images/Slider1MOB.jpg'),
            'alt'     => 'Al Reem Expo imported products and international delivery services',
            'href'    => route('products.index'),
        ],
        [
            'desktop' => asset('images/Slider2IMG.jpg'),
            'mobile'  => asset('images/Slider2MOB.jpg'),
            'alt'     => 'Al Reem Expo integrated import, sourcing and wholesale supply solutions',
            'href'    => route('products.index'),
        ],
        [
            'desktop' => asset('images/Slider3IMG.jpg'),
            'mobile'  => asset('images/Slider3MOB.jpg'),
            'alt'     => 'Al Reem Expo trusted global sourcing and fast secure delivery',
            'href'    => route('products.index'),
        ],
    ];

    $categories = $topCategories ?? collect();

    $whatsAppNumber = '96181161440';
    $whatsAppMessage = 'Hello Al Reem Expo, I would like to ask about your products and wholesale options.';
@endphp

<style>
    [x-cloak] {
        display: none !important;
    }

    .home-product-scroll {
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .home-product-scroll::-webkit-scrollbar {
        display: none;
    }

    @media (prefers-reduced-motion: reduce) {
        .home-motion {
            scroll-behavior: auto !important;
            transition-duration: 0.01ms !important;
            animation-duration: 0.01ms !important;
        }
    }
</style>

<div class="overflow-hidden rounded-3xl bg-slate-50 text-slate-900">

    {{-- ========================================================= --}}
    {{-- HERO --}}
    {{-- ========================================================= --}}
    <section class="relative overflow-hidden border-b border-slate-200 bg-white">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-72 bg-gradient-to-b from-sky-50 via-blue-50/50 to-transparent" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -left-24 top-8 h-72 w-72 rounded-full bg-cyan-200/25 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -right-24 top-24 h-80 w-80 rounded-full bg-blue-300/20 blur-3xl" aria-hidden="true"></div>

        <div class="relative mx-auto max-w-7xl px-4 pb-10 pt-9 sm:px-6 sm:pb-14 sm:pt-12 lg:px-8 lg:pb-16 lg:pt-16">
            <div class="grid items-end gap-7 lg:grid-cols-[minmax(0,1fr)_auto] lg:gap-10">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 rounded-full border border-sky-200 bg-white/90 px-3.5 py-2 text-xs font-bold text-sky-800 shadow-sm backdrop-blur">
                        <span class="h-2 w-2 rounded-full bg-emerald-500" aria-hidden="true"></span>
                        Imported products • sourcing • wholesale supply
                    </div>

                    <h1 class="mt-5 max-w-3xl text-4xl font-black tracking-[-0.035em] text-slate-950 sm:text-5xl lg:text-6xl lg:leading-[1.05]">
                        Quality products from trusted sources,
                        <span class="text-blue-700">delivered with confidence.</span>
                    </h1>

                    <p class="mt-5 max-w-2xl text-base leading-7 text-slate-600 sm:text-lg sm:leading-8">
                        Discover carefully selected imported products for everyday needs, backed by dependable sourcing, competitive value and professional service in Lebanon.
                    </p>

                    <div class="mt-7 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                        <a
                            href="{{ route('products.index') }}"
                            class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-blue-700 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-700/15 transition hover:-translate-y-0.5 hover:bg-blue-800 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:text-base"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h2l2.2 10.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.6L20 7H6"></path>
                                <circle cx="10" cy="20" r="1"></circle>
                                <circle cx="18" cy="20" r="1"></circle>
                            </svg>
                            Shop Products
                        </a>

                        <a
                            href="#categories"
                            class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-6 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:text-base"
                        >
                            Browse Categories
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"></path>
                            </svg>
                        </a>
                    </div>

                    <div class="mt-7 flex flex-wrap gap-x-6 gap-y-3 text-sm font-semibold text-slate-600">
                        <span class="inline-flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-emerald-700" aria-hidden="true">✓</span>
                            Selected imported products
                        </span>
                        <span class="inline-flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-emerald-700" aria-hidden="true">✓</span>
                            Responsive customer support
                        </span>
                    </div>
                </div>

                <div class="hidden rounded-2xl border border-blue-100 bg-white/80 p-4 shadow-sm backdrop-blur lg:block">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-blue-700">Al Reem Expo</p>
                    <p class="mt-1 max-w-[15rem] text-sm leading-6 text-slate-600">
                        Retail, sourcing and wholesale solutions in one place.
                    </p>
                </div>
            </div>

            {{-- Promotional slider --}}
            <div
                class="home-motion group relative mt-9 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_22px_70px_-30px_rgba(15,23,42,0.35)] sm:rounded-3xl"
                x-data="homeSlideshow(@js($homeSlides), 6500)"
                @mouseenter="hovered = true"
                @mouseleave="hovered = false"
                @focusin="focusWithin = true"
                @focusout="focusWithin = false"
                @keydown.left.prevent="previous(true)"
                @keydown.right.prevent="next(true)"
                @touchstart.passive="handleTouchStart($event)"
                @touchend="handleTouchEnd($event)"
                @click.capture="handleClickCapture($event)"
                role="region"
                aria-roledescription="carousel"
                aria-label="Al Reem Expo promotional banners"
                tabindex="0"
            >
                <div class="relative aspect-[4/5] overflow-hidden bg-white md:aspect-[21/9]">
                    <template x-for="(slide, index) in slides" :key="slide.desktop">
                        <a
                            x-show="current === index"
                            x-cloak
                            :href="slide.href"
                            :aria-label="slide.alt"
                            :aria-hidden="current === index ? 'false' : 'true'"
                            class="absolute inset-0 block"
                            x-transition:enter="transition-opacity duration-500 ease-out"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition-opacity duration-300 ease-in"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                        >
                            <picture class="block h-full w-full">
                                <source media="(max-width: 767px)" :srcset="slide.mobile">
                                <img
                                    :src="slide.desktop"
                                    :alt="slide.alt"
                                    :loading="index === 0 ? 'eager' : 'lazy'"
                                    :fetchpriority="index === 0 ? 'high' : 'auto'"
                                    width="1920"
                                    height="820"
                                    draggable="false"
                                    class="h-full w-full select-none object-contain"
                                >
                            </picture>
                        </a>
                    </template>

                    <noscript>
                        <picture class="block h-full w-full">
                            <source media="(max-width: 767px)" srcset="{{ asset('images/Slider1MOB.jpg') }}">
                            <img
                                src="{{ asset('images/Slider1IMG.jpg') }}"
                                alt="Al Reem Expo imported products and delivery services"
                                width="1920"
                                height="820"
                                class="h-full w-full object-contain"
                            >
                        </picture>
                    </noscript>
                </div>

                <button
                    x-show="slides.length > 1"
                    x-cloak
                    type="button"
                    @click.stop="previous(true)"
                    aria-label="Show previous promotional banner"
                    class="absolute left-4 top-1/2 z-20 hidden h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-white/50 bg-slate-950/55 text-white shadow-lg backdrop-blur-md transition hover:bg-slate-950/80 focus:outline-none focus:ring-2 focus:ring-white sm:inline-flex"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"></path>
                    </svg>
                </button>

                <button
                    x-show="slides.length > 1"
                    x-cloak
                    type="button"
                    @click.stop="next(true)"
                    aria-label="Show next promotional banner"
                    class="absolute right-4 top-1/2 z-20 hidden h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-white/50 bg-slate-950/55 text-white shadow-lg backdrop-blur-md transition hover:bg-slate-950/80 focus:outline-none focus:ring-2 focus:ring-white sm:inline-flex"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"></path>
                    </svg>
                </button>

                <div
                    x-show="slides.length > 1"
                    x-cloak
                    class="absolute bottom-3 left-1/2 z-20 flex -translate-x-1/2 items-center gap-2 rounded-full bg-slate-950/45 px-3 py-2 shadow-lg backdrop-blur-md sm:bottom-4"
                >
                    <template x-for="(slide, index) in slides" :key="index">
                        <button
                            type="button"
                            @click.stop="goTo(index)"
                            class="h-2.5 rounded-full transition-all duration-300"
                            :class="current === index ? 'w-8 bg-white' : 'w-2.5 bg-white/50 hover:bg-white/80'"
                            :aria-label="`Show promotional banner ${index + 1}`"
                            :aria-current="current === index ? 'true' : 'false'"
                        ></button>
                    </template>
                </div>

                <p class="sr-only" aria-live="polite" x-text="slides[current]?.alt"></p>
            </div>

            {{-- Trust strip --}}
            <div class="mt-8 grid overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm sm:grid-cols-2 lg:grid-cols-4">
                <div class="flex items-center gap-3 border-b border-slate-200 p-4 sm:border-r lg:border-b-0">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Global Sourcing</h2>
                        <p class="mt-0.5 text-xs leading-5 text-slate-500">Dependable supplier network</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 border-b border-slate-200 p-4 lg:border-b-0 lg:border-r">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M3 7h11v10H3zM14 10h4l3 3v4h-7z"></path>
                            <circle cx="7" cy="18" r="2"></circle>
                            <circle cx="18" cy="18" r="2"></circle>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Reliable Delivery</h2>
                        <p class="mt-0.5 text-xs leading-5 text-slate-500">Professional logistics support</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 border-b border-slate-200 p-4 sm:border-b-0 sm:border-r">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Wholesale Value</h2>
                        <p class="mt-0.5 text-xs leading-5 text-slate-500">Competitive business pricing</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Helpful Support</h2>
                        <p class="mt-0.5 text-xs leading-5 text-slate-500">Fast customer assistance</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================================================= --}}
    {{-- CATEGORIES --}}
    {{-- ========================================================= --}}
    <section id="categories" class="scroll-mt-28 bg-slate-50 py-12 sm:py-16 lg:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex items-end justify-between gap-6">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.2em] text-blue-700 sm:text-sm">Shop by category</p>
                    <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Find what you need faster</h2>
                    <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600 sm:text-base">
                        Browse our main categories and get directly to the products that matter to you.
                    </p>
                </div>

                <a href="{{ route('products.index') }}" class="hidden shrink-0 items-center gap-2 text-sm font-bold text-blue-700 transition hover:text-blue-900 sm:inline-flex">
                    View all products
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"></path>
                    </svg>
                </a>
            </div>

            @if($categories->count())
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-5 md:grid-cols-4 lg:grid-cols-6">
                    @foreach($categories as $category)
                        <a
                            href="{{ route('products.byCategory', $category->slug) }}"
                            class="group relative flex min-w-0 flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white p-3 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-blue-200 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:p-4"
                        >
                            <div class="relative flex aspect-square items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-slate-50 to-slate-100">
                                @if($category->image)
                                    <img
                                        src="{{ asset('storage/' . $category->image) }}"
                                        alt="{{ $category->name }}"
                                        width="220"
                                        height="220"
                                        loading="lazy"
                                        class="h-full w-full object-contain p-4 transition duration-300 group-hover:scale-105"
                                    >
                                @else
                                    <svg class="h-11 w-11 text-slate-300 sm:h-12 sm:w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 8-9-5-9 5 9 5 9-5Z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m3 8 9 5 9-5M3 12l9 5 9-5"></path>
                                    </svg>
                                @endif
                            </div>

                            <div class="mt-3 flex min-h-[44px] items-center justify-between gap-2">
                                <h3 class="line-clamp-2 text-sm font-bold leading-5 text-slate-800 transition group-hover:text-blue-700">
                                    {{ $category->name }}
                                </h3>
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500 transition group-hover:bg-blue-700 group-hover:text-white" aria-hidden="true">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"></path>
                                    </svg>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
                    <svg class="mx-auto h-11 w-11 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 8-9-5-9 5 9 5 9-5Z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="m3 8 9 5 9-5M3 12l9 5 9-5"></path>
                    </svg>
                    <p class="mt-3 font-semibold text-slate-700">No categories are available yet.</p>
                </div>
            @endif

            <div class="mt-6 sm:hidden">
                <a href="{{ route('products.index') }}" class="inline-flex min-h-12 w-full items-center justify-center rounded-xl border border-blue-200 bg-white px-4 py-3 text-sm font-bold text-blue-700 shadow-sm transition hover:bg-blue-50">
                    View all products
                </a>
            </div>
        </div>
    </section>

    {{-- ========================================================= --}}
    {{-- NEW ARRIVALS --}}
    {{-- ========================================================= --}}
    @if(isset($newProducts) && $newProducts->count())
        <section class="border-y border-slate-200 bg-white py-12 sm:py-16 lg:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-8 flex items-end justify-between gap-6">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.2em] text-blue-700 sm:text-sm">New arrivals</p>
                        <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Fresh additions to the store</h2>
                        <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600 sm:text-base">
                            Explore recently added products. Swipe through the collection on mobile or browse the full grid on larger screens.
                        </p>
                    </div>

                    <a href="{{ route('products.index') }}" class="hidden shrink-0 items-center gap-2 text-sm font-bold text-blue-700 transition hover:text-blue-900 sm:inline-flex">
                        Shop all products
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"></path>
                        </svg>
                    </a>
                </div>

                <div class="home-product-scroll home-motion -mx-4 flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-5 sm:-mx-6 sm:px-6 md:mx-0 md:grid md:grid-cols-3 md:gap-6 md:overflow-visible md:px-0 md:pb-0 lg:grid-cols-4">
                    @foreach($newProducts as $product)
                        <div class="w-[82vw] max-w-[19rem] shrink-0 snap-start md:w-auto md:max-w-none">
                            @include('products.partials._card', ['product' => $product])
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 sm:hidden">
                    <a href="{{ route('products.index') }}" class="inline-flex min-h-12 w-full items-center justify-center rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm font-bold text-blue-700 transition hover:bg-blue-100">
                        Shop all products
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- ========================================================= --}}
    {{-- FEATURED PRODUCTS --}}
    {{-- Only appears when the controller supplies $featuredProducts. --}}
    {{-- ========================================================= --}}
    @if(isset($featuredProducts) && $featuredProducts->count())
        <section class="bg-slate-50 py-12 sm:py-16 lg:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-8 flex items-end justify-between gap-6">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.2em] text-blue-700 sm:text-sm">Featured selection</p>
                        <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Products worth discovering</h2>
                        <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600 sm:text-base">
                            A curated selection of highlighted products from across the Al Reem Expo catalog.
                        </p>
                    </div>

                    <a href="{{ route('products.index') }}" class="hidden shrink-0 items-center gap-2 text-sm font-bold text-blue-700 transition hover:text-blue-900 sm:inline-flex">
                        Explore catalog
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"></path>
                        </svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 md:grid-cols-3 md:gap-6 lg:grid-cols-4">
                    @foreach($featuredProducts as $product)
                        @include('products.partials._card', ['product' => $product])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ========================================================= --}}
    {{-- WHOLESALE / SUPPORT CTA --}}
    {{-- ========================================================= --}}
    <section class="bg-white py-12 sm:py-16 lg:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden rounded-3xl bg-slate-950 px-6 py-10 text-white shadow-2xl sm:px-10 sm:py-12 lg:grid lg:grid-cols-[minmax(0,1fr)_auto] lg:items-center lg:gap-12 lg:px-14 lg:py-14">
                <div class="pointer-events-none absolute -right-20 -top-24 h-72 w-72 rounded-full bg-blue-600/30 blur-3xl" aria-hidden="true"></div>
                <div class="pointer-events-none absolute -bottom-28 left-1/3 h-72 w-72 rounded-full bg-cyan-500/15 blur-3xl" aria-hidden="true"></div>

                <div class="relative max-w-2xl">
                    <p class="text-xs font-black uppercase tracking-[0.2em] text-blue-300 sm:text-sm">Wholesale & sourcing</p>
                    <h2 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">
                        Need larger quantities or help finding a product?
                    </h2>
                    <p class="mt-4 max-w-xl text-sm leading-7 text-slate-300 sm:text-base">
                        Talk directly with Al Reem Expo about wholesale quantities, sourcing opportunities, product availability and business requirements.
                    </p>
                </div>

                <div class="relative mt-8 flex flex-col gap-3 sm:flex-row lg:mt-0 lg:flex-col xl:flex-row">
                    <a
                        href="https://wa.me/{{ $whatsAppNumber }}?text={{ urlencode($whatsAppMessage) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-emerald-500 px-6 py-3 font-bold text-white shadow-lg transition hover:-translate-y-0.5 hover:bg-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-300 focus:ring-offset-2 focus:ring-offset-slate-950"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 32 32" fill="currentColor" aria-hidden="true">
                            <path d="M16.04 2.003A13.9 13.9 0 0 0 4.11 23.06L2 30l7.12-2.03a13.97 13.97 0 1 0 6.92-25.967Zm0 25.45a11.45 11.45 0 0 1-5.84-1.6l-.42-.25-4.23 1.21 1.13-4.12-.27-.43a11.45 11.45 0 1 1 9.63 5.19Zm6.28-8.57c-.34-.17-2.03-1-2.35-1.11-.31-.11-.54-.17-.77.17-.23.34-.89 1.11-1.09 1.34-.2.23-.4.26-.74.09-.34-.17-1.44-.53-2.74-1.69-1.01-.9-1.7-2.02-1.9-2.36-.2-.34-.02-.52.15-.69.15-.15.34-.4.51-.6.17-.2.23-.34.34-.57.11-.23.06-.43-.03-.6-.09-.17-.77-1.86-1.06-2.55-.28-.67-.56-.58-.77-.59h-.66c-.23 0-.6.09-.91.43-.31.34-1.2 1.17-1.2 2.86s1.23 3.32 1.4 3.55c.17.23 2.42 3.69 5.86 5.17.82.35 1.46.56 1.96.72.82.26 1.57.22 2.16.13.66-.1 2.03-.83 2.32-1.63.29-.8.29-1.49.2-1.63-.09-.14-.31-.23-.65-.4Z"></path>
                        </svg>
                        Ask on WhatsApp
                    </a>

                    <a
                        href="{{ route('products.index') }}"
                        class="inline-flex min-h-12 items-center justify-center rounded-xl border border-white/20 bg-white/10 px-6 py-3 font-bold text-white backdrop-blur transition hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white/70 focus:ring-offset-2 focus:ring-offset-slate-950"
                    >
                        Browse Products
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection
