@extends('layouts.app')

@section('title', 'Alreem | Global Import & Export Trading Company')

@section(
    'meta_description',
    'Alreem is a global import and export trading company specializing in wholesale sourcing, international logistics, and supply chain solutions across multiple industries.'
)

@section('canonical', route('home'))
@section('robots', 'index,follow')

@section('content')

@php
    $homeSlides = [
        [
            'desktop' => asset('images/Slider1IMG.jpg'),
            'mobile'  => asset('images/Slider1MOB.jpg'),
            'alt'     => 'Alreem Expo imported products and international delivery services',
            'href'    => route('products.index'),
        ],
        [
            'desktop' => asset('images/Slider2IMG.jpg'),
            'mobile'  => asset('images/Slider2MOB.jpg'),
            'alt'     => 'Alreem Expo integrated import, sourcing and wholesale supply solutions',
            'href'    => route('products.index'),
        ],
        [
            'desktop' => asset('images/Slider3IMG.jpg'),
            'mobile'  => asset('images/Slider3MOB.jpg'),
            'alt'     => 'Alreem Expo trusted global sourcing and fast secure delivery',
            'href'    => route('products.index'),
        ],
    ];
@endphp

<style>
    [x-cloak] {
        display: none !important;
    }

    /*
     * Mobile product row:
     * keep horizontal scrolling without displaying a distracting scrollbar.
     */
    .home-product-scroll {
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .home-product-scroll::-webkit-scrollbar {
        display: none;
    }

    /*
     * Persistent cart icon.
     *
     * The icon is generated with CSS instead of being placed inside the
     * button markup. It therefore remains visible even when existing
     * JavaScript changes button.innerHTML or button.textContent.
     */
    .home-cart-icon {
        font-size: 0;
        line-height: 0;
    }

    .home-cart-icon::before {
        content: "";
        display: block;
        width: 1.25rem;
        height: 1.25rem;
        background-color: currentColor;

        -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='none' stroke='black' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.9' d='M3 4h2l2.4 10.4A2 2 0 0 0 9.35 16h7.75a2 2 0 0 0 1.95-1.55L21 7H6M9 20h.01M19 20h.01M15 10h4m-2-2v4'/%3E%3C/svg%3E");

        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='none' stroke='black' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.9' d='M3 4h2l2.4 10.4A2 2 0 0 0 9.35 16h7.75a2 2 0 0 0 1.95-1.55L21 7H6M9 20h.01M19 20h.01M15 10h4m-2-2v4'/%3E%3C/svg%3E");

        -webkit-mask-repeat: no-repeat;
        mask-repeat: no-repeat;

        -webkit-mask-position: center;
        mask-position: center;

        -webkit-mask-size: contain;
        mask-size: contain;
    }

    @media (prefers-reduced-motion: reduce) {
        .home-motion {
            scroll-behavior: auto !important;
            transition-duration: 0.01ms !important;
            animation-duration: 0.01ms !important;
        }
    }
</style>

<div class="min-h-screen bg-slate-50 text-slate-900">

    {{-- ========================================================= --}}
    {{-- HERO --}}
    {{-- ========================================================= --}}
    <section class="relative overflow-hidden border-b border-slate-200/80 bg-white">

        {{-- Subtle background decoration --}}
        <div
            class="pointer-events-none absolute -left-32 top-0 h-80 w-80 rounded-full bg-blue-100/70 blur-3xl"
            aria-hidden="true"
        ></div>

        <div
            class="pointer-events-none absolute -right-32 bottom-0 h-80 w-80 rounded-full bg-indigo-100/60 blur-3xl"
            aria-hidden="true"
        ></div>

        <div class="relative mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8 lg:py-16">

            {{-- Hero heading and actions --}}
            <div class="mb-7 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <div
                        class="mb-4 inline-flex items-center gap-2 rounded-full
                               border border-blue-200 bg-blue-50 px-3 py-1.5
                               text-xs font-semibold text-blue-800"
                    >
                        <span class="h-2 w-2 rounded-full bg-green-500"></span>
                        Global import, sourcing and wholesale supply
                    </div>

                    <h1
                        class="text-3xl font-bold tracking-tight text-slate-950
                               sm:text-4xl lg:text-5xl"
                    >
                        Global sourcing, quality products,
                        <span class="text-blue-700">reliable delivery.</span>
                    </h1>

                    <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-600 sm:text-base">
                        Carefully selected imported products supported by dependable
                        suppliers, competitive pricing and professional delivery services.
                    </p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row lg:shrink-0">
                    <a
                        href="{{ route('products.index') }}"
                        class="inline-flex min-h-12 items-center justify-center gap-2
                               rounded-xl bg-blue-700 px-6 py-3 font-semibold text-white
                               shadow-sm transition hover:bg-blue-800 hover:shadow-md
                               focus:outline-none focus:ring-2 focus:ring-blue-500
                               focus:ring-offset-2"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            class="h-5 w-5"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M4 7h16M5.5 7l1 13h11l1-13M9 11v5M15 11v5M9 7V4h6v3"
                            />
                        </svg>

                        Shop Products
                    </a>

                    <a
                        href="#categories"
                        class="inline-flex min-h-12 items-center justify-center gap-2
                               rounded-xl border border-slate-300 bg-white px-6 py-3
                               font-semibold text-slate-700 shadow-sm transition
                               hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700
                               focus:outline-none focus:ring-2 focus:ring-blue-500
                               focus:ring-offset-2"
                    >
                        Browse Categories

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            class="h-4 w-4"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m9 18 6-6-6-6"
                            />
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Responsive promotional slider --}}
            <div
                class="home-motion group relative overflow-hidden rounded-2xl
                       border border-slate-200 bg-white shadow-xl sm:rounded-3xl"
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
                aria-label="Alreem Expo promotional banners"
                tabindex="0"
            >
                {{--
                    Mobile uses the 4:5 artwork.
                    Tablet and desktop use the wide artwork.
                --}}
                <div class="relative aspect-[4/5] overflow-hidden bg-white md:aspect-[21/9]">
                    <template
                        x-for="(slide, index) in slides"
                        :key="slide.desktop"
                    >
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
                                <source
                                    media="(max-width: 767px)"
                                    :srcset="slide.mobile"
                                >

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

                    {{-- No-JavaScript fallback --}}
                    <noscript>
                        <picture class="block h-full w-full">
                            <source
                                media="(max-width: 767px)"
                                srcset="{{ asset('images/Slider1MOB.jpg') }}"
                            >

                            <img
                                src="{{ asset('images/Slider1IMG.jpg') }}"
                                alt="Alreem Expo imported products and delivery services"
                                width="1920"
                                height="820"
                                class="h-full w-full object-contain"
                            >
                        </picture>
                    </noscript>
                </div>

                {{-- Previous slide --}}
                <button
                    x-show="slides.length > 1"
                    x-cloak
                    type="button"
                    @click.stop="previous(true)"
                    aria-label="Show previous promotional banner"
                    class="absolute left-4 top-1/2 z-20 hidden h-11 w-11
                           -translate-y-1/2 items-center justify-center rounded-full
                           border border-white/40 bg-slate-950/55 text-white shadow-lg
                           backdrop-blur-md transition hover:bg-slate-950/75
                           focus:outline-none focus:ring-2 focus:ring-white sm:inline-flex"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        class="h-5 w-5"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m15 18-6-6 6-6"
                        />
                    </svg>
                </button>

                {{-- Next slide --}}
                <button
                    x-show="slides.length > 1"
                    x-cloak
                    type="button"
                    @click.stop="next(true)"
                    aria-label="Show next promotional banner"
                    class="absolute right-4 top-1/2 z-20 hidden h-11 w-11
                           -translate-y-1/2 items-center justify-center rounded-full
                           border border-white/40 bg-slate-950/55 text-white shadow-lg
                           backdrop-blur-md transition hover:bg-slate-950/75
                           focus:outline-none focus:ring-2 focus:ring-white sm:inline-flex"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        class="h-5 w-5"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m9 18 6-6-6-6"
                        />
                    </svg>
                </button>

                {{-- Slide indicators --}}
                <div
                    x-show="slides.length > 1"
                    x-cloak
                    class="absolute bottom-3 left-1/2 z-20 flex
                           -translate-x-1/2 items-center gap-2 rounded-full
                           bg-slate-950/45 px-3 py-2 shadow-lg backdrop-blur-md
                           sm:bottom-4"
                >
                    <template x-for="(slide, index) in slides" :key="index">
                        <button
                            type="button"
                            @click.stop="goTo(index)"
                            class="h-2.5 rounded-full transition-all duration-300"
                            :class="current === index
                                ? 'w-8 bg-white'
                                : 'w-2.5 bg-white/50 hover:bg-white/80'"
                            :aria-label="`Show promotional banner ${index + 1}`"
                            :aria-current="current === index ? 'true' : 'false'"
                        ></button>
                    </template>
                </div>

                {{-- Accessible slide announcement --}}
                <p
                    class="sr-only"
                    aria-live="polite"
                    x-text="slides[current]?.alt"
                ></p>
            </div>

            {{-- Trust strip --}}
            <div
                class="mt-10 grid overflow-hidden rounded-2xl border border-slate-200
                       bg-white shadow-sm sm:grid-cols-2 lg:grid-cols-4"
            >
                <div class="flex items-center gap-3 border-b border-slate-200 p-4 sm:border-r">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center
                               rounded-xl bg-blue-50 text-blue-700"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            class="h-5 w-5"
                            aria-hidden="true"
                        >
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"></path>
                        </svg>
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">
                            Global Sourcing
                        </h2>

                        <p class="mt-0.5 text-xs text-slate-500">
                            Reliable supplier network
                        </p>
                    </div>
                </div>

                <div
                    class="flex items-center gap-3 border-b border-slate-200 p-4
                           sm:border-r lg:border-b-0"
                >
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center
                               rounded-xl bg-green-50 text-green-700"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            class="h-5 w-5"
                            aria-hidden="true"
                        >
                            <path d="M3 7h11v10H3zM14 10h4l3 3v4h-7z"></path>
                            <circle cx="7" cy="18" r="2"></circle>
                            <circle cx="18" cy="18" r="2"></circle>
                        </svg>
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">
                            Shipping Support
                        </h2>

                        <p class="mt-0.5 text-xs text-slate-500">
                            Dependable logistics assistance
                        </p>
                    </div>
                </div>

                <div
                    class="flex items-center gap-3 border-b border-slate-200 p-4
                           sm:border-b-0 sm:border-r"
                >
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center
                               rounded-xl bg-indigo-50 text-indigo-700"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            class="h-5 w-5"
                            aria-hidden="true"
                        >
                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">
                            Wholesale Value
                        </h2>

                        <p class="mt-0.5 text-xs text-slate-500">
                            Competitive bulk pricing
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-4">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center
                               rounded-xl bg-amber-50 text-amber-700"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            class="h-5 w-5"
                            aria-hidden="true"
                        >
                            <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"></path>
                        </svg>
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">
                            Helpful Support
                        </h2>

                        <p class="mt-0.5 text-xs text-slate-500">
                            Fast and professional responses
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================================================= --}}
    {{-- CATEGORIES --}}
    {{-- ========================================================= --}}
    <section id="categories" class="scroll-mt-24 py-12 sm:py-16 lg:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="mb-8 flex items-end justify-between gap-5">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.16em] text-blue-700">
                        Shop by category
                    </p>

                    <h2 class="mt-2 text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">
                        Find what you need faster
                    </h2>

                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">
                        Browse our main product categories and quickly reach the products
                        most relevant to your needs.
                    </p>
                </div>

                <a
                    href="{{ route('products.index') }}"
                    class="hidden shrink-0 items-center gap-1.5 text-sm font-semibold
                           text-blue-700 transition hover:text-blue-900 sm:inline-flex"
                >
                    View all products

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        class="h-4 w-4"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m9 18 6-6-6-6"
                        />
                    </svg>
                </a>
            </div>

            @php
                $categories = $topCategories ?? collect();
            @endphp

            @if($categories->count())
                <div
                    class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-5
                           md:grid-cols-4 lg:grid-cols-6"
                >
                    @foreach($categories as $category)
                        <a
                            href="{{ route('products.byCategory', $category->slug) }}"
                            class="group flex min-w-0 flex-col overflow-hidden rounded-2xl
                                   border border-slate-200 bg-white p-3 shadow-sm
                                   transition duration-200 hover:-translate-y-1
                                   hover:border-blue-200 hover:shadow-lg
                                   focus:outline-none focus:ring-2 focus:ring-blue-500
                                   focus:ring-offset-2 sm:p-4"
                        >
                            <div
                                class="flex aspect-square items-center justify-center
                                       overflow-hidden rounded-xl bg-slate-50"
                            >
                                @if($category->image)
                                    <img
                                        src="{{ asset('storage/' . $category->image) }}"
                                        alt="{{ $category->name }}"
                                        width="180"
                                        height="180"
                                        loading="lazy"
                                        class="h-full w-full object-contain p-3
                                               transition duration-300 group-hover:scale-105"
                                    >
                                @else
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        class="h-10 w-10 text-slate-300 sm:h-12 sm:w-12"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="m21 8-9-5-9 5 9 5 9-5Z"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="m3 8 9 5 9-5M3 12l9 5 9-5"
                                        />
                                    </svg>
                                @endif
                            </div>

                            <h3
                                class="mt-3 line-clamp-2 min-h-[40px] text-center
                                       text-sm font-semibold leading-5 text-slate-800
                                       transition group-hover:text-blue-700"
                            >
                                {{ $category->name }}
                            </h3>
                        </a>
                    @endforeach
                </div>
            @else
                <div
                    class="rounded-2xl border border-dashed border-slate-300
                           bg-white px-6 py-10 text-center"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.5"
                        class="mx-auto h-10 w-10 text-slate-300"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m21 8-9-5-9 5 9 5 9-5Z"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m3 8 9 5 9-5M3 12l9 5 9-5"
                        />
                    </svg>

                    <p class="mt-3 font-medium text-slate-700">
                        No categories are available yet.
                    </p>
                </div>
            @endif

            <div class="mt-6 sm:hidden">
                <a
                    href="{{ route('products.index') }}"
                    class="inline-flex min-h-12 w-full items-center justify-center
                           rounded-xl border border-blue-200 bg-white px-4 py-3
                           text-sm font-semibold text-blue-700 transition
                           hover:bg-blue-50"
                >
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

                <div class="mb-8 flex items-end justify-between gap-5">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-[0.16em] text-blue-700">
                            New arrivals
                        </p>

                        <h2 class="mt-2 text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">
                            Recently added products
                        </h2>

                        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">
                            Explore our latest available products. Swipe on mobile or browse
                            the complete grid on larger screens.
                        </p>
                    </div>

                    <a
                        href="{{ route('products.index') }}"
                        class="hidden shrink-0 items-center gap-1.5 text-sm font-semibold
                               text-blue-700 transition hover:text-blue-900 sm:inline-flex"
                    >
                        View all products

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            class="h-4 w-4"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m9 18 6-6-6-6"
                            />
                        </svg>
                    </a>
                </div>

                <div
                    class="home-product-scroll home-motion -mx-4 flex snap-x
                           snap-mandatory gap-4 overflow-x-auto px-4 pb-4
                           sm:-mx-6 sm:px-6 md:mx-0 md:grid md:grid-cols-3
                           md:gap-6 md:overflow-visible md:px-0 md:pb-0
                           lg:grid-cols-4"
                >
                    @foreach($newProducts as $product)
                        @php
                            $primaryImage = $product->images->firstWhere('is_primary', true)
                                ?? $product->images->first();

                            $imageUrl = $primaryImage?->image
                                ? asset('storage/' . $primaryImage->image)
                                : asset('images/placeholder.png');

                            $price = (float) $product->price;
                            $discountPrice = (float) $product->discount_price;

                            $hasDiscount = $discountPrice > 0
                                && $price > 0
                                && $discountPrice < $price;

                            $discountPercentage = $hasDiscount
                                ? (int) round((($price - $discountPrice) / $price) * 100)
                                : null;

                            $stock = (int) ($product->stock ?? 0);
                            $isOutOfStock = $stock <= 0;
                            $isLowStock = !$isOutOfStock && $stock <= 5;
                        @endphp

                        <article
                            class="group flex w-[82vw] max-w-[19rem] shrink-0 snap-start
                                   flex-col overflow-hidden rounded-2xl border
                                   border-slate-200 bg-white shadow-sm transition
                                   duration-200 hover:-translate-y-1
                                   hover:border-blue-200 hover:shadow-xl
                                   md:w-auto md:max-w-none"
                        >
                            {{-- Product image --}}
                            <a
                                href="{{ route('products.show', $product) }}"
                                aria-label="View {{ $product->name }}"
                                class="relative flex h-52 items-center justify-center
                                       overflow-hidden bg-slate-50
                                       focus:outline-none focus:ring-2
                                       focus:ring-inset focus:ring-blue-500"
                            >
                                <img
                                    src="{{ $imageUrl }}"
                                    alt="{{ $product->name }}"
                                    width="360"
                                    height="280"
                                    loading="lazy"
                                    class="max-h-full max-w-full object-contain p-5
                                           transition duration-300 group-hover:scale-105"
                                >

                                {{-- Product badges --}}
                                <div
                                    class="pointer-events-none absolute left-3 top-3
                                           flex flex-col items-start gap-2"
                                >
                                    <span
                                        class="rounded-full bg-blue-700 px-2.5 py-1
                                               text-[11px] font-bold uppercase tracking-wide
                                               text-white shadow-sm"
                                    >
                                        New
                                    </span>

                                    @if($hasDiscount)
                                        <span
                                            class="rounded-full bg-red-500 px-2.5 py-1
                                                   text-[11px] font-bold text-white shadow-sm"
                                        >
                                            Save {{ $discountPercentage }}%
                                        </span>
                                    @endif
                                </div>

                                @if($isOutOfStock)
                                    <span
                                        class="absolute right-3 top-3 rounded-full
                                               bg-slate-900 px-2.5 py-1 text-[11px]
                                               font-semibold text-white shadow-sm"
                                    >
                                        Out of stock
                                    </span>
                                @elseif($isLowStock)
                                    <span
                                        class="absolute right-3 top-3 rounded-full
                                               bg-amber-500 px-2.5 py-1 text-[11px]
                                               font-semibold text-white shadow-sm"
                                    >
                                        {{ $stock }} left
                                    </span>
                                @endif
                            </a>

                            <div class="flex flex-1 flex-col p-4">

                                {{-- Product title --}}
                                <a
                                    href="{{ route('products.show', $product) }}"
                                    class="line-clamp-2 min-h-[48px] text-base font-semibold
                                           leading-6 text-slate-900 transition
                                           hover:text-blue-700 focus:outline-none
                                           focus:text-blue-700"
                                >
                                    {{ \Illuminate\Support\Str::limit($product->name, 65) }}
                                </a>

                                {{-- Price --}}
                                <div class="mt-3 flex flex-wrap items-baseline gap-x-2 gap-y-1">
                                    <p class="text-xl font-bold text-blue-700">
                                        ${{ number_format((float) $product->display_price, 2) }}
                                    </p>

                                    @if($hasDiscount)
                                        <p class="text-sm text-slate-400 line-through">
                                            ${{ number_format($price, 2) }}
                                        </p>
                                    @endif
                                </div>

                                {{-- Availability --}}
                                <div class="mt-2">
                                    @if($isOutOfStock)
                                        <p
                                            class="inline-flex items-center gap-1.5
                                                   text-xs font-medium text-slate-500"
                                        >
                                            <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                                            Currently unavailable
                                        </p>
                                    @elseif($isLowStock)
                                        <p
                                            class="inline-flex items-center gap-1.5
                                                   text-xs font-medium text-amber-700"
                                        >
                                            <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                                            Limited stock available
                                        </p>
                                    @else
                                        <p
                                            class="inline-flex items-center gap-1.5
                                                   text-xs font-medium text-green-700"
                                        >
                                            <span class="h-2 w-2 rounded-full bg-green-500"></span>
                                            In stock
                                        </p>
                                    @endif
                                </div>

                                {{-- Product actions --}}
                                <div class="mt-auto flex items-stretch gap-2 pt-5">
                                    <a
                                        href="{{ route('products.show', $product) }}"
                                        class="inline-flex min-h-11 flex-1 items-center
                                               justify-center rounded-xl bg-slate-900
                                               px-3 py-2.5 text-sm font-semibold text-white
                                               transition hover:bg-blue-700
                                               focus:outline-none focus:ring-2
                                               focus:ring-blue-500 focus:ring-offset-2"
                                    >
                                        View Product
                                    </a>

                                    <button
                                        type="button"
                                        class="add-to-cart home-cart-icon inline-flex
                                               min-h-11 w-12 shrink-0 items-center
                                               justify-center overflow-hidden rounded-xl
                                               border border-blue-200 bg-blue-50
                                               text-blue-700 shadow-sm transition
                                               duration-200 hover:border-blue-700
                                               hover:bg-blue-700 hover:text-white
                                               active:scale-95
                                               focus:outline-none focus:ring-2
                                               focus:ring-blue-500 focus:ring-offset-2
                                               disabled:cursor-not-allowed
                                               disabled:border-slate-200
                                               disabled:bg-slate-100
                                               disabled:text-slate-400
                                               disabled:shadow-none"
                                        data-id="{{ $product->id }}"
                                        data-product-name="{{ $product->name }}"
                                        aria-label="{{ $isOutOfStock
                                            ? $product->name . ' is out of stock'
                                            : 'Add ' . $product->name . ' to cart' }}"
                                        title="{{ $isOutOfStock
                                            ? 'Out of stock'
                                            : 'Add to cart' }}"
                                        @disabled($isOutOfStock)
                                    >
                                        Add to cart
                                    </button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-5 sm:hidden">
                    <a
                        href="{{ route('products.index') }}"
                        class="inline-flex min-h-12 w-full items-center
                               justify-center rounded-xl border border-blue-200
                               bg-white px-4 py-3 text-sm font-semibold
                               text-blue-700 transition hover:bg-blue-50"
                    >
                        View all products
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- ========================================================= --}}
    {{-- WHOLESALE CTA --}}
    {{-- ========================================================= --}}
    <section class="py-12 sm:py-16 lg:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div
                class="relative overflow-hidden rounded-3xl bg-slate-950
                       px-6 py-10 text-white shadow-xl sm:px-10 sm:py-12
                       lg:flex lg:items-center lg:justify-between lg:gap-10
                       lg:px-14"
            >
                <div
                    class="pointer-events-none absolute -right-16 -top-20
                           h-64 w-64 rounded-full bg-blue-600/30 blur-3xl"
                    aria-hidden="true"
                ></div>

                <div
                    class="pointer-events-none absolute -bottom-24 left-1/3
                           h-64 w-64 rounded-full bg-indigo-600/20 blur-3xl"
                    aria-hidden="true"
                ></div>

                <div class="relative max-w-2xl">
                    <p
                        class="text-sm font-bold uppercase tracking-[0.18em]
                               text-blue-300"
                    >
                        Wholesale and sourcing
                    </p>

                    <h2
                        class="mt-3 text-3xl font-bold tracking-tight
                               sm:text-4xl"
                    >
                        Looking for products in larger quantities?
                    </h2>

                    <p class="mt-4 leading-7 text-slate-300">
                        Explore our available products and discover sourcing
                        opportunities supported by competitive wholesale pricing
                        and reliable international trade experience.
                    </p>
                </div>

                <div
                    class="relative mt-8 flex flex-col gap-3
                           sm:flex-row lg:mt-0 lg:shrink-0"
                >
                    <a
                        href="{{ route('products.index') }}"
                        class="inline-flex min-h-12 items-center justify-center
                               rounded-xl bg-blue-600 px-6 py-3 font-semibold
                               text-white transition hover:bg-blue-500
                               focus:outline-none focus:ring-2
                               focus:ring-blue-400 focus:ring-offset-2
                               focus:ring-offset-slate-950"
                    >
                        Browse Products
                    </a>

                    <a
                        href="#categories"
                        class="inline-flex min-h-12 items-center justify-center
                               rounded-xl border border-white/20 bg-white/10
                               px-6 py-3 font-semibold text-white backdrop-blur-sm
                               transition hover:bg-white/20
                               focus:outline-none focus:ring-2
                               focus:ring-white/70 focus:ring-offset-2
                               focus:ring-offset-slate-950"
                    >
                        View Categories
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection
