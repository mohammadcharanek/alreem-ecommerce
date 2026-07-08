@extends('layouts.app')

@section('title', 'Alreem | Global Import & Export Trading Company')
@section('meta_description', 'Alreem is a global import and export trading company specializing in wholesale sourcing, international logistics, and supply chain solutions across multiple industries.')
@section('canonical', route('home'))
@section('robots', 'index,follow')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-[#F8F6F0] via-[#FCFBF8] to-[#F4F1E8]">

    <!-- HERO SECTION -->
    <section class="relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 py-12 sm:py-16 lg:py-24">

            <div class="flex flex-col lg:flex-row items-center justify-between gap-10 lg:gap-12 mb-12 lg:mb-16">

                <!-- LEFT -->
                <div class="flex-1 text-center lg:text-left">

                    <!-- Brand -->
                    <div class="flex items-center justify-center lg:justify-start gap-4 mb-6">
                        <img src="{{ Storage::url('logo.jpeg') }}"
                             alt="Alreem Logo"
                             class="w-16 h-16 rounded-2xl shadow-lg ring-2 ring-white object-cover">

                        <div>
                            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight">
                                <span class="text-green-500">Al</span>
                                <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-800 bg-clip-text text-transparent">
                                    Reem
                                </span>
                            </h1>

                            <div class="mx-auto lg:mx-0 mt-2 w-24 h-1 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full"></div>
                        </div>
                    </div>

                    <h2 class="text-xl lg:text-2xl text-gray-700 font-medium mb-6 leading-relaxed">
                        Global Import & Export • Wholesale Supply • International Trade Solutions
                    </h2>

                    <p class="text-gray-600 text-base sm:text-lg mb-8 max-w-xl mx-auto lg:mx-0">
                        We connect manufacturers, suppliers, and buyers worldwide with efficient sourcing,
                        competitive wholesale pricing, and reliable logistics support.
                    </p>

                    <!-- CTA -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="{{ route('products.index') }}"
                           class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all">
                            Explore Products
                        </a>

                        <a href="#categories"
                           class="inline-flex items-center justify-center px-8 py-4 bg-white/70 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:border-indigo-400 hover:text-indigo-600 hover:bg-white transition">
                            Browse Categories
                        </a>
                    </div>
                </div>

                <!-- RIGHT FEATURES -->
                <div class="flex-1 w-full max-w-md">
                    <div class="grid gap-5">

                        <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-5 sm:p-6 shadow-lg ring-1 ring-gray-100 hover:shadow-xl transition">
                            <div class="flex gap-4 items-center">
                                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-xl">🌍</div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Global Sourcing</h3>
                                    <p class="text-sm text-gray-600">International supplier network</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-5 sm:p-6 shadow-lg ring-1 ring-gray-100 hover:shadow-xl transition">
                            <div class="flex gap-4 items-center">
                                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-xl">🚢</div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Logistics Support</h3>
                                    <p class="text-sm text-gray-600">Fast and reliable shipping</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-5 sm:p-6 shadow-lg ring-1 ring-gray-100 hover:shadow-xl transition">
                            <div class="flex gap-4 items-center">
                                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-xl">💰</div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Wholesale Pricing</h3>
                                    <p class="text-sm text-gray-600">Competitive bulk rates</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <!-- HERO SLIDESHOW -->
            <div class="relative w-full overflow-hidden rounded-2xl sm:rounded-3xl shadow-2xl ring-1 ring-black/5 bg-white"
                 style="aspect-ratio: 1942 / 809;"
                 x-data="slideshow()"
                 x-init="start()">

                <template x-for="(image, index) in images" :key="index">
                    <div x-show="current === index"
                         x-transition.opacity.duration.700ms
                         class="absolute inset-0">
                        <img :src="image"
                             alt="Alreem import export banner"
                             class="w-full h-full object-cover">
                    </div>
                </template>

                <!-- Overlay -->
                <div class="absolute inset-0 bg-gradient-to-r from-black/20 via-transparent to-transparent pointer-events-none"></div>

                <!-- Dots -->
                <div class="absolute bottom-4 left-0 right-0 flex items-center justify-center gap-2">
                    <template x-for="(image, index) in images" :key="index">
                        <button type="button"
                                @click="current = index"
                                class="h-2.5 rounded-full transition-all"
                                :class="current === index ? 'w-8 bg-white' : 'w-2.5 bg-white/60 hover:bg-white'">
                        </button>
                    </template>
                </div>

            </div>

        </div>
    </section>

    <!-- CATEGORIES -->
    <section id="categories" class="py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4">

            <div class="flex items-end justify-between gap-4 mb-8">
                <div>
                    <p class="text-sm font-semibold text-blue-700 uppercase tracking-wide">Browse</p>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Product Categories</h2>
                </div>

                <a href="{{ route('products.index') }}"
                   class="hidden sm:inline-flex text-sm font-semibold text-blue-700 hover:text-blue-900">
                    View all products
                </a>
            </div>

            @php $cats = ($topCategories ?? collect()); @endphp

            @if($cats->count())
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 sm:gap-6">
                    @foreach($cats as $cat)
                        <a href="{{ route('products.byCategory', $cat->slug) }}"
                           class="group bg-white/90 rounded-2xl shadow-sm hover:shadow-xl p-4 text-center ring-1 ring-gray-100 transition-all hover:-translate-y-1">

                            <div class="h-20 flex items-center justify-center rounded-xl bg-gray-50">
                                @if($cat->image)
                                    <img src="{{ asset('storage/'.$cat->image) }}"
                                         alt="{{ $cat->name }}"
                                         class="max-h-full object-contain group-hover:scale-105 transition">
                                @else
                                    <div class="text-3xl text-gray-300">📦</div>
                                @endif
                            </div>

                            <div class="mt-3 font-semibold text-sm sm:text-base text-gray-800 group-hover:text-blue-700 transition">
                                {{ $cat->name }}
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl bg-white p-6 text-gray-500 shadow-sm ring-1 ring-gray-100">
                    No categories available yet.
                </div>
            @endif

        </div>
    </section>

    <!-- LATEST PRODUCTS HORIZONTAL CAROUSEL -->
    @if(isset($newProducts) && $newProducts->count())
    <section class="py-12 sm:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">

            <div class="flex items-end justify-between gap-4 mb-8">
                <div>
                    <p class="text-sm font-semibold text-blue-700 uppercase tracking-wide">New arrivals</p>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Latest Products</h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Swipe on mobile or scroll horizontally to explore our newest products.
                    </p>
                </div>

                <a href="{{ route('products.index') }}"
                   class="hidden sm:inline-flex items-center justify-center rounded-xl border border-blue-200 px-4 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-50 transition">
                    View all
                </a>
            </div>

            <div class="-mx-4 px-4 overflow-x-auto pb-4 snap-x snap-mandatory">
                <div class="flex gap-4 sm:gap-6 min-w-full">

                    @foreach($newProducts as $product)
                        @php
                            $img = optional($product->images->first());
                            $imgUrl = $img?->image ? asset('storage/'.$img->image) : asset('images/placeholder.png');
                            $hasDiscount = $product->discount_price && $product->discount_price < $product->price;
                        @endphp

                        <a href="{{ route('products.show', $product) }}"
                           class="group snap-start shrink-0 w-[78%] sm:w-[45%] md:w-[31%] lg:w-[23%] bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:shadow-xl transition-all hover:-translate-y-1">

                            <div class="relative h-44 sm:h-48 rounded-2xl bg-gray-50 flex items-center justify-center overflow-hidden">
                                <img src="{{ $imgUrl }}"
                                     alt="{{ $product->name }}"
                                     class="max-h-full max-w-full object-contain p-3 group-hover:scale-105 transition duration-300">

                                <span class="absolute left-3 top-3 rounded-full bg-blue-600 px-3 py-1 text-xs font-semibold text-white shadow">
                                    New
                                </span>
                            </div>

                            <div class="mt-4">
                                <h3 class="min-h-[44px] font-semibold text-gray-900 group-hover:text-blue-700 transition">
                                    {{ Str::limit($product->name, 55) }}
                                </h3>

                                <div class="mt-3 flex items-center gap-2">
                                    <p class="text-lg font-bold text-indigo-600">
                                        ${{ number_format($product->display_price, 2) }}
                                    </p>

                                    @if($hasDiscount)
                                        <p class="text-sm text-gray-400 line-through">
                                            ${{ number_format($product->price, 2) }}
                                        </p>
                                    @endif
                                </div>

                                <div class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white group-hover:bg-blue-700 transition">
                                    View Product
                                </div>
                            </div>
                        </a>
                    @endforeach

                </div>
            </div>

            <div class="mt-4 sm:hidden">
                <a href="{{ route('products.index') }}"
                   class="inline-flex w-full items-center justify-center rounded-xl border border-blue-200 px-4 py-3 text-sm font-semibold text-blue-700 hover:bg-blue-50 transition">
                    View all products
                </a>
            </div>

        </div>
    </section>
    @endif

    <!-- TRUST SECTION -->
    <section class="py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-3 gap-6 sm:gap-8">

            <div class="rounded-2xl bg-white/80 p-6 text-center shadow-sm ring-1 ring-gray-100">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-xl">✓</div>
                <h3 class="font-bold text-xl text-gray-900">Verified Suppliers</h3>
                <p class="text-gray-600 mt-2">Trusted global manufacturing partners</p>
            </div>

            <div class="rounded-2xl bg-white/80 p-6 text-center shadow-sm ring-1 ring-gray-100">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 text-xl">🔒</div>
                <h3 class="font-bold text-xl text-gray-900">Secure Trade</h3>
                <p class="text-gray-600 mt-2">Safe international transactions</p>
            </div>

            <div class="rounded-2xl bg-white/80 p-6 text-center shadow-sm ring-1 ring-gray-100">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-purple-100 text-xl">⚡</div>
                <h3 class="font-bold text-xl text-gray-900">Fast Response</h3>
                <p class="text-gray-600 mt-2">Dedicated support for B2B clients</p>
            </div>

        </div>
    </section>

</div>

<!-- SLIDESHOW SCRIPT -->
<script>
function slideshow() {
    return {
        images: [
            '{{ asset('images/slider2exp1.jpg') }}',
            '{{ asset('images/slider3.jpg') }}',
            '{{ asset('images/slider4.jpg') }}',
        ],
        current: 0,
        interval: null,

        start() {
            this.interval = setInterval(() => {
                this.current = (this.current + 1) % this.images.length;
            }, 5000);
        }
    }
}
</script>

@endsection