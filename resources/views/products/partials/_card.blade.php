{{-- resources/views/products/partials/_card.blade.php --}}

@php
    $primaryImage = $product->images->first();
    $imageUrl = $primaryImage?->url ?: asset('images/placeholder.png');
    $imageAlt = $primaryImage?->alt ?: $product->name;

    $hasDiscount = $product->has_discount;
    $inStock = $product->is_in_stock;
    $availableStock = $product->available_stock;
    $inWishlist = in_array($product->id, $wishlistProducts ?? [], true);
    $productMeta = $product->brand?->name ?: $product->category?->name;
@endphp

<article class="group relative flex h-full flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-lg focus-within:ring-2 focus-within:ring-brand-blue focus-within:ring-offset-2">
    {{-- Wishlist control remains outside the product link. --}}
    @auth
        <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST" class="wishlist-fallback-form absolute right-2 top-2 z-20">
            @csrf
            <button
                type="submit"
                class="wishlist-btn inline-flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 bg-white/95 text-xl shadow-sm backdrop-blur transition hover:scale-105 hover:border-rose-200 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 disabled:cursor-wait disabled:opacity-60 {{ $inWishlist ? 'is-active' : '' }}"
                data-id="{{ $product->id }}"
                data-in="{{ $inWishlist ? '1' : '0' }}"
                data-mode="toggle"
                aria-pressed="{{ $inWishlist ? 'true' : 'false' }}"
                aria-label="{{ $inWishlist ? 'Remove ' . $product->name . ' from wishlist' : 'Add ' . $product->name . ' to wishlist' }}"
                title="{{ $inWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}"
            >
                <span data-wishlist-icon aria-hidden="true">{{ $inWishlist ? '❤️' : '🤍' }}</span>
            </button>
        </form>
    @else
        <a href="{{ route('login') }}"
           class="absolute right-2 top-2 z-20 inline-flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 bg-white/95 text-xl shadow-sm transition hover:scale-105 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2"
           aria-label="Sign in to add {{ $product->name }} to your wishlist"
           title="Sign in to use wishlist">
            <span aria-hidden="true">🤍</span>
        </a>
    @endauth

    <a href="{{ route('products.show', $product) }}" class="relative block overflow-hidden bg-gray-50">
        <div class="aspect-square w-full overflow-hidden">
            <img
                src="{{ $imageUrl }}"
                alt="{{ $imageAlt }}"
                loading="lazy"
                decoding="async"
                width="600"
                height="600"
                class="h-full w-full object-contain p-3 transition duration-300 group-hover:scale-[1.03]"
            >
        </div>

        <div class="absolute left-2 top-2 flex max-w-[calc(100%-3.5rem)] flex-wrap gap-1.5">
            @if($product->is_new)
                <span class="rounded-full bg-emerald-600 px-2 py-1 text-[10px] font-bold uppercase tracking-wide text-white sm:text-xs">
                    New
                </span>
            @endif

            @if($product->featured)
                <span class="rounded-full bg-sky-600 px-2 py-1 text-[10px] font-bold uppercase tracking-wide text-white sm:text-xs">
                    Featured
                </span>
            @endif

            @if($hasDiscount)
                <span class="rounded-full bg-rose-600 px-2 py-1 text-[10px] font-bold text-white sm:text-xs">
                    -{{ $product->discount_percentage }}%
                </span>
            @endif
        </div>
    </a>

    <div class="flex flex-1 flex-col p-3 sm:p-4">
        @if($productMeta)
            <p class="mb-1 truncate text-[11px] font-semibold uppercase tracking-wide text-gray-500 sm:text-xs">
                {{ $productMeta }}
            </p>
        @else
            <div class="mb-1 h-4" aria-hidden="true"></div>
        @endif

        <a href="{{ route('products.show', $product) }}"
           class="rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-blue">
            <h3 class="line-clamp-2 min-h-10 text-sm font-bold leading-5 text-gray-900 transition group-hover:text-brand-blue sm:text-base sm:leading-6">
                {{ $product->name }}
            </h3>
        </a>

        @if((float) $product->rating > 0)
            <div class="mt-2 flex items-center gap-1 text-xs text-gray-600" aria-label="Rated {{ number_format($product->rating, 1) }} out of 5">
                <span class="text-amber-500" aria-hidden="true">★</span>
                <span class="font-semibold">{{ number_format($product->rating, 1) }}</span>
                <span class="text-gray-400">/ 5</span>
            </div>
        @else
            <div class="mt-2 h-4" aria-hidden="true"></div>
        @endif

        <div class="mt-3 flex min-h-8 flex-wrap items-baseline gap-x-2 gap-y-1">
            <span class="text-lg font-extrabold text-gray-950 sm:text-xl">
                ${{ number_format($product->display_price, 2) }}
            </span>
            @if($hasDiscount)
                <span class="text-xs font-medium text-gray-400 line-through sm:text-sm">
                    ${{ number_format($product->price, 2) }}
                </span>
            @endif
        </div>

        <div class="mt-2 min-h-5 text-xs font-semibold">
            @if(!$inStock)
                <span class="inline-flex items-center gap-1 text-gray-600">
                    <span class="h-2 w-2 rounded-full bg-gray-400" aria-hidden="true"></span>
                    Out of stock
                </span>
            @elseif($availableStock <= 5)
                <span class="inline-flex items-center gap-1 text-amber-700">
                    <span class="h-2 w-2 rounded-full bg-amber-500" aria-hidden="true"></span>
                    Only {{ $availableStock }} left
                </span>
            @else
                <span class="inline-flex items-center gap-1 text-emerald-700">
                    <span class="h-2 w-2 rounded-full bg-emerald-500" aria-hidden="true"></span>
                    In stock
                </span>
            @endif
        </div>

        <div class="mt-auto flex items-stretch gap-2 pt-4">
            <button
                type="button"
                class="add-to-cart inline-flex min-h-11 flex-1 items-center justify-center gap-1.5 rounded-xl px-3 text-sm font-bold transition focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2 disabled:cursor-not-allowed {{ $inStock ? 'bg-brand-blue text-white hover:brightness-95' : 'bg-gray-200 text-gray-500' }}"
                data-id="{{ $product->id }}"
                aria-label="{{ $inStock ? 'Add ' . $product->name . ' to cart' : $product->name . ' is out of stock' }}"
                title="{{ $inStock ? 'Add to cart' : 'Out of stock' }}"
                @disabled(!$inStock)
            >
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M3 3h2l2.2 10.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.6L20 7H6"></path>
                    <circle cx="10" cy="20" r="1"></circle>
                    <circle cx="18" cy="20" r="1"></circle>
                </svg>
                <span>{{ $inStock ? 'Add to cart' : 'Unavailable' }}</span>
            </button>

            <a href="{{ route('products.show', $product) }}"
               class="inline-flex min-h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-gray-300 bg-white text-gray-700 transition hover:border-brand-blue hover:text-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2"
               aria-label="View details for {{ $product->name }}"
               title="View details">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="m9 18 6-6-6-6"></path>
                </svg>
            </a>
        </div>
    </div>
</article>
