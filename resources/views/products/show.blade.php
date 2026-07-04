@extends('layouts.app')

@section('title', $product->name)

@section('content')
@php
    $galleryImages = $product->images->sortBy(function ($i) {
        return [($i->is_primary ? 0 : 1), $i->sort_order ?? 9999, $i->id];
    })->values();

    $hasDiscount = !is_null($product->discount_price) && $product->discount_price > 0;
    $unit = $hasDiscount ? $product->discount_price : $product->price;

    $inStock = (int) ($product->stock ?? 0) > 0;

    $inWishlist = $inWishlist
        ?? (isset($wishlistProducts) ? in_array($product->id, (array) $wishlistProducts) : false);
@endphp

<div class="max-w-6xl mx-auto">
    {{-- Flash messages --}}
    @if (session('success'))
        <div class="mb-4 rounded bg-green-100 p-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded bg-red-100 p-3 text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-8 rounded bg-white p-6 shadow md:grid-cols-2">
        {{-- Gallery --}}
        <div>
            @if ($galleryImages->isNotEmpty())
                <div class="grid grid-cols-2 gap-3">
                    @foreach ($galleryImages as $image)
                        <a
                            href="{{ asset('storage/' . $image->image) }}"
                            class="glightbox"
                            data-gallery="product-gallery"
                            data-title="{{ $product->name }}"
                        >
                            <img
                                src="{{ asset('storage/' . $image->image) }}"
                                alt="{{ $image->alt ?: $product->name }}"
                                class="h-56 w-full rounded border bg-gray-50 object-contain"
                                loading="lazy"
                            />
                        </a>
                    @endforeach
                </div>
            @else
                <div class="flex h-64 w-full items-center justify-center rounded border bg-gray-100">
                    <span class="text-gray-500">No images available</span>
                </div>
            @endif
        </div>

        {{-- Info / Buy box --}}
        <div>
            <h1 class="mb-2 text-2xl font-bold">
                {{ $product->name }}
            </h1>

            <div class="mb-4 flex items-baseline gap-3">
                <div class="text-2xl font-semibold text-green-700">
                    ${{ number_format($unit, 2) }}
                </div>

                @if($hasDiscount)
                    <div class="text-gray-400 line-through">
                        ${{ number_format($product->price, 2) }}
                    </div>
                @endif
            </div>

            <p class="mb-2 text-sm text-gray-600">
                SKU: {{ $product->sku ?? '—' }}
            </p>

            @if($inStock)
                <p class="mb-4 text-sm text-green-700">
                    In stock: {{ (int) $product->stock }}
                </p>
            @else
                <p class="mb-4 text-sm text-red-600">
                    Out of stock
                </p>
            @endif

            <div class="mb-6 flex items-center gap-3">
                <label for="qty-{{ $product->id }}" class="text-sm">
                    Qty
                </label>

                <input
                    id="qty-{{ $product->id }}"
                    type="number"
                    min="1"
                    max="{{ max(1, (int) ($product->stock ?? 1)) }}"
                    value="1"
                    class="w-20 rounded border px-2 py-1"
                />

                <button
                    type="button"
                    class="add-to-cart rounded bg-green-600 px-4 py-2 text-white transition hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-50"
                    data-id="{{ $product->id }}"
                    data-qty-input="#qty-{{ $product->id }}"
                    @disabled(!$inStock)
                >
                    Add to Cart
                </button>
            </div>

            {{-- Wishlist --}}
            @auth
                <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST" class="inline wishlist-fallback-form">
                    @csrf

                    <button
                        type="submit"
                        class="wishlist-btn text-pink-600 hover:underline {{ $inWishlist ? 'is-active' : '' }}"
                        data-id="{{ $product->id }}"
                        data-in="{{ $inWishlist ? '1' : '0' }}"
                        data-mode="toggle"
                        aria-pressed="{{ $inWishlist ? 'true' : 'false' }}"
                        title="{{ $inWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}"
                    >
                        {!! $inWishlist ? '❤️ In wishlist (click to remove)' : '🤍 Add to Wishlist' !!}
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-pink-600 hover:underline">
                    🤍 Add to Wishlist
                </a>
            @endauth

            <div class="prose mt-6 max-w-none">
                {!! nl2br(e($product->description ?? '')) !!}
            </div>
        </div>
    </div>

    {{-- Related products --}}
    @if(isset($related) && $related->count())
        <h2 class="mt-10 mb-4 text-xl font-bold">
            You may also like
        </h2>

        <div class="grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-4">
            @foreach($related as $rel)
                @include('products.partials._card', [
                    'product' => $rel,
                    'wishlistProducts' => $wishlistProducts ?? [],
                ])
            @endforeach
        </div>
    @endif
</div>
@endsection