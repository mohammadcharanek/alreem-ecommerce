@extends('layouts.app')

@section('title', 'Alreem | Global Import & Export Trading Company')
@section('meta_description', 'Alreem is a global import and export trading company specializing in wholesale sourcing, international logistics, and supply chain solutions across multiple industries.')
@section('canonical', route('home'))
@section('robots', 'index,follow')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-[#F8F6F0] via-[#FCFBF8] to-[#F4F1E8]">

    <!-- HERO SECTION -->
    <section class="relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 py-16 lg:py-24">

            <div class="flex flex-col lg:flex-row items-center justify-between gap-12 mb-16">

                <!-- LEFT -->
                <div class="flex-1 text-center lg:text-left">

                    <!-- Brand -->
                    <div class="flex items-center justify-center lg:justify-start gap-4 mb-6">
                        <img src="{{ Storage::url('logo.jpeg') }}"
                             alt="Alreem Logo"
                             class="w-16 h-16 rounded-2xl shadow-lg ring-2 ring-white">

                        <div>
                          <h1 class="text-4xl lg:text-6xl font-bold tracking-tight">
    <span class="text-green-500">Al</span>
    <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-800 bg-clip-text text-transparent">
        Reem
    </span>
</h1>
                            <div class="w-24 h-1 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full"></div>
                        </div>
                    </div>

                    <h2 class="text-xl lg:text-2xl text-gray-700 font-medium mb-6 leading-relaxed">
                        Global Import & Export • Wholesale Supply • International Trade Solutions
                    </h2>

                    <p class="text-gray-600 text-lg mb-8 max-w-xl">
                        We connect manufacturers, suppliers, and buyers worldwide with efficient sourcing,
                        competitive wholesale pricing, and reliable logistics support.
                    </p>

                    <!-- CTA -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">

                        <a href="{{ route('products.index') }}"
                           class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all">
                            Explore Products
                        </a>

                        <a href="#categories"
                           class="inline-flex items-center justify-center px-8 py-4 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:border-indigo-400 hover:text-indigo-600 transition">
                            Browse Categories
                        </a>

                    </div>
                </div>

                <!-- RIGHT FEATURES -->
                <div class="flex-1 max-w-md">

                    <div class="grid gap-6">

                        <div class="bg-white/70 backdrop-blur-sm rounded-2xl p-6 shadow-lg">
                            <div class="flex gap-4 items-center">
                                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">🌍</div>
                                <div>
                                    <h3 class="font-semibold">Global Sourcing</h3>
                                    <p class="text-sm text-gray-600">International supplier network</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white/70 backdrop-blur-sm rounded-2xl p-6 shadow-lg">
                            <div class="flex gap-4 items-center">
                                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">🚢</div>
                                <div>
                                    <h3 class="font-semibold">Logistics Support</h3>
                                    <p class="text-sm text-gray-600">Fast and reliable shipping</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white/70 backdrop-blur-sm rounded-2xl p-6 shadow-lg">
                            <div class="flex gap-4 items-center">
                                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">💰</div>
                                <div>
                                    <h3 class="font-semibold">Wholesale Pricing</h3>
                                    <p class="text-sm text-gray-600">Competitive bulk rates</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <!-- SLIDESHOW -->
            <div class="relative w-full overflow-hidden rounded-3xl shadow-2xl"
                 style="aspect-ratio: 1942 / 809;"
                 x-data="slideshow()"
                 x-init="start()">

                <template x-for="(image, index) in images" :key="index">
                    <div x-show="current === index"
                         x-transition
                         class="absolute inset-0">
                        <img :src="image" class="w-full h-full object-cover">
                    </div>
                </template>

            </div>

        </div>
    </section>

    <!-- CATEGORIES -->
    <section id="categories" class="py-16">
        <div class="max-w-7xl mx-auto px-4">

            <h2 class="text-3xl font-bold mb-8">Product Categories</h2>

            @php $cats = ($topCategories ?? collect()); @endphp

            @if($cats->count())
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                    @foreach($cats as $cat)
                        <a href="{{ route('products.byCategory', $cat->slug) }}"
                           class="bg-white rounded-2xl shadow hover:shadow-lg p-4 text-center">
                            <div class="h-20 flex items-center justify-center">
                                @if($cat->image)
                                    <img src="{{ asset('storage/'.$cat->image) }}"
                                         class="max-h-full">
                                @endif
                            </div>
                            <div class="mt-3 font-semibold">{{ $cat->name }}</div>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No categories available yet.</p>
            @endif

        </div>
    </section>

    <!-- NEW PRODUCTS -->
    @if(isset($newProducts) && $newProducts->count())
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">

            <h2 class="text-3xl font-bold mb-8">Latest Products</h2>

            <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-6">

                @foreach($newProducts as $product)
                <a href="{{ route('products.show', $product) }}"
                   class="bg-white border rounded-2xl p-4 hover:shadow-lg">

                    @php
                        $img = optional($product->images->first());
                        $imgUrl = $img?->image ? asset('storage/'.$img->image) : asset('images/placeholder.png');
                    @endphp

                    <img src="{{ $imgUrl }}" class="h-40 mx-auto object-contain">

                    <h3 class="mt-4 font-semibold">{{ $product->name }}</h3>

                    <p class="text-indigo-600 font-bold mt-2">
                        ${{ number_format($product->display_price, 2) }}
                    </p>

                </a>
                @endforeach

            </div>

        </div>
    </section>
    @endif

    <!-- TRUST SECTION -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-3 gap-8">

            <div class="text-center">
                <h3 class="font-bold text-xl">Verified Suppliers</h3>
                <p class="text-gray-600 mt-2">Trusted global manufacturing partners</p>
            </div>

            <div class="text-center">
                <h3 class="font-bold text-xl">Secure Trade</h3>
                <p class="text-gray-600 mt-2">Safe international transactions</p>
            </div>

            <div class="text-center">
                <h3 class="font-bold text-xl">Fast Response</h3>
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
        start() {
            setInterval(() => {
                this.current = (this.current + 1) % this.images.length;
            }, 5000);
        }
    }
}
</script>

@endsection