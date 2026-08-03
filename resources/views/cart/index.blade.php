@extends('layouts.app')

@section('title', 'Shopping Cart')
@section('canonical', route('cart.index'))
@section('robots', 'noindex,nofollow')

@section('content')
    @php
        $cart = $cart ?? session('cart', []);
        $lines = [];
        $subtotal = 0;
        $totalItems = 0;

        foreach ($products as $product) {
            $quantity = (int) ($cart[$product->id] ?? 0);

            if ($quantity <= 0) {
                continue;
            }

            $regularPrice = (float) $product->price;
            $discountPrice = (float) ($product->discount_price ?? 0);

            $unitPrice = $discountPrice > 0
                ? $discountPrice
                : $regularPrice;

            $lineTotal = $unitPrice * $quantity;

            $subtotal += $lineTotal;
            $totalItems += $quantity;

            $image = $product->images
                ->sortBy(fn ($item) => [
                    $item->is_primary ? 0 : 1,
                    $item->sort_order ?? 9999,
                    $item->id,
                ])
                ->first();

            $imagePath = $image?->image;

            $imageUrl = $imagePath
                ? asset('storage/' . $imagePath)
                : asset('images/placeholder.png');

            $imageAlt = $image?->alt ?: $product->name;

            $lines[] = [
                'product' => $product,
                'quantity' => $quantity,
                'regularPrice' => $regularPrice,
                'unitPrice' => $unitPrice,
                'lineTotal' => $lineTotal,
                'imageUrl' => $imageUrl,
                'imageAlt' => $imageAlt,
            ];
        }
    @endphp

    <div class="bg-gray-50">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
            {{-- Page header --}}
            <div class="mb-8">
                <nav
                    class="mb-4 flex items-center gap-2 text-sm text-gray-500"
                    aria-label="Breadcrumb"
                >
                    <a
                        href="{{ route('home') }}"
                        class="transition hover:text-gray-900"
                    >
                        Home
                    </a>

                    <svg
                        class="h-4 w-4"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.24 4.24a.75.75 0 0 1 0 1.06l-4.24 4.24a.75.75 0 0 1-1.08 0Z"
                            clip-rule="evenodd"
                        />
                    </svg>

                    <span class="font-medium text-gray-900">
                        Shopping Cart
                    </span>
                </nav>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                            Your shopping cart
                        </h1>

                        <p class="mt-2 text-sm leading-6 text-gray-600">
                            Review your products and quantities before checkout.
                        </p>
                    </div>

                    @if (!empty($lines))
                        <p
                            id="cart-page-count-label"
                            class="text-sm font-medium text-gray-600"
                            aria-live="polite"
                        >
                            <span id="cart-page-count">{{ $totalItems }}</span>
                            <span id="cart-page-count-word">
                                {{ Str::plural('item', $totalItems) }}
                            </span>
                        </p>
                    @endif
                </div>
            </div>

            {{-- Flash messages --}}
            @if (session('success'))
                <div
                    class="mb-6 rounded-2xl border border-green-200 bg-green-50 p-4 text-sm text-green-800"
                    role="status"
                >
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div
                    class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800"
                    role="alert"
                >
                    {{ session('error') }}
                </div>
            @endif

            @if (empty($lines))
                {{-- Empty cart --}}
                <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white px-6 py-14 text-center shadow-sm sm:px-10 sm:py-20">
                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                        <svg
                            class="h-10 w-10"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M2.25 3h1.386a1.5 1.5 0 0 1 1.445 1.092l.383 1.358m0 0h14.294l-1.5 6.75H7.36m-1.896-6.75L7.36 12.2m0 0-1.102 1.653a1.5 1.5 0 0 0 1.248 2.332h9.744m-9.75 3.065h.008v.008H7.5v-.008Zm9 0h.008v.008H16.5v-.008Z"
                            />
                        </svg>
                    </div>

                    <h2 class="mt-6 text-2xl font-bold text-gray-900">
                        Your cart is empty
                    </h2>

                    <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-gray-600">
                        Explore our products and add something you like to your cart.
                    </p>

                    <a
                        href="{{ route('products.index') }}"
                        class="mt-7 inline-flex min-h-12 items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Continue shopping

                        <svg
                            class="ml-2 h-4 w-4"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M3.75 10a.75.75 0 0 1 .75-.75h9.69L10.72 5.78a.75.75 0 1 1 1.06-1.06l4.75 4.75a.75.75 0 0 1 0 1.06l-4.75 4.75a.75.75 0 1 1-1.06-1.06l3.47-3.47H4.5a.75.75 0 0 1-.75-.75Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </a>
                </div>
            @else
                <div class="grid items-start gap-8 lg:grid-cols-[minmax(0,1fr)_380px]">
                    {{-- Products --}}
                    <section
                        class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm"
                        aria-labelledby="cart-items-heading"
                    >
                        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-5 sm:px-6">
                            <div>
                                <h2
                                    id="cart-items-heading"
                                    class="text-lg font-bold text-gray-900"
                                >
                                    Cart items
                                </h2>

                                <p class="mt-1 text-sm text-gray-500">
                                    Update quantities or remove products.
                                </p>
                            </div>

                            <form
                                action="{{ route('cart.clear') }}"
                                method="POST"
                                onsubmit="return confirm('Remove all products from your cart?');"
                            >
                                @csrf

                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M8.75 1.75a.75.75 0 0 0-.75.75V3H4.5a.75.75 0 0 0 0 1.5h.443l.77 12.313A1.75 1.75 0 0 0 7.46 18.5h5.08a1.75 1.75 0 0 0 1.747-1.687l.77-12.313h.443a.75.75 0 0 0 0-1.5H12v-.5a.75.75 0 0 0-.75-.75h-2.5ZM8.5 6.75a.75.75 0 0 0-1.5 0v7.5a.75.75 0 0 0 1.5 0v-7.5Zm4.5 0a.75.75 0 0 0-1.5 0v7.5a.75.75 0 0 0 1.5 0v-7.5Z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>

                                    <span class="hidden sm:inline">
                                        Clear cart
                                    </span>
                                </button>
                            </form>
                        </div>

                        <div
                            id="cart-items-list"
                            class="divide-y divide-gray-100"
                        >
                            @foreach ($lines as $row)
                                @php
                                    /** @var \App\Models\Product $product */
                                    $product = $row['product'];
                                    $quantity = $row['quantity'];
                                    $regularPrice = $row['regularPrice'];
                                    $unitPrice = $row['unitPrice'];
                                    $lineTotal = $row['lineTotal'];
                                    $imageUrl = $row['imageUrl'];
                                    $imageAlt = $row['imageAlt'];

                                    $stock = max(
                                        0,
                                        (int) ($product->stock ?? 0)
                                    );

                                    $hasDiscount =
                                        $unitPrice < $regularPrice;
                                @endphp

                                <article
                                    class="p-4 sm:p-6"
                                    data-cart-row
                                    data-product-id="{{ $product->id }}"
                                >
                                    <div class="flex gap-4 sm:gap-5">
                                        {{-- Product image --}}
                                        <a
                                            href="{{ route('products.show', $product) }}"
                                            class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-gray-200 bg-gray-50 p-2 transition hover:border-indigo-300 sm:h-32 sm:w-32"
                                            aria-label="View {{ $product->name }}"
                                        >
                                            <img
                                                src="{{ $imageUrl }}"
                                                alt="{{ $imageAlt }}"
                                                loading="lazy"
                                                width="128"
                                                height="128"
                                                class="h-full w-full object-contain"
                                            >
                                        </a>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <a
                                                        href="{{ route('products.show', $product) }}"
                                                        class="line-clamp-2 text-base font-bold leading-6 text-gray-900 transition hover:text-indigo-600 sm:text-lg"
                                                    >
                                                        {{ $product->name }}
                                                    </a>

                                                    @if ($product->sku)
                                                        <p class="mt-1 text-xs text-gray-500">
                                                            SKU: {{ $product->sku }}
                                                        </p>
                                                    @endif
                                                </div>

                                                {{-- Desktop remove button --}}
                                                <form
                                                    action="{{ route('cart.remove', $product) }}"
                                                    method="POST"
                                                    class="hidden shrink-0 sm:block"
                                                    onsubmit="return confirm('Remove this product from your cart?');"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="rounded-lg p-2 text-gray-400 transition hover:bg-red-50 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-500"
                                                        aria-label="Remove {{ $product->name }}"
                                                        title="Remove product"
                                                    >
                                                        <svg
                                                            class="h-5 w-5"
                                                            viewBox="0 0 20 20"
                                                            fill="currentColor"
                                                            aria-hidden="true"
                                                        >
                                                            <path
                                                                fill-rule="evenodd"
                                                                d="M8.75 1.75a.75.75 0 0 0-.75.75V3H4.5a.75.75 0 0 0 0 1.5h.443l.77 12.313A1.75 1.75 0 0 0 7.46 18.5h5.08a1.75 1.75 0 0 0 1.747-1.687l.77-12.313h.443a.75.75 0 0 0 0-1.5H12v-.5a.75.75 0 0 0-.75-.75h-2.5ZM8.5 6.75a.75.75 0 0 0-1.5 0v7.5a.75.75 0 0 0 1.5 0v-7.5Zm4.5 0a.75.75 0 0 0-1.5 0v7.5a.75.75 0 0 0 1.5 0v-7.5Z"
                                                                clip-rule="evenodd"
                                                            />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>

                                            {{-- Price --}}
                                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                                <span class="text-base font-bold text-gray-900">
                                                    ${{ number_format($unitPrice, 2) }}
                                                </span>

                                                @if ($hasDiscount)
                                                    <span class="text-sm text-gray-400 line-through">
                                                        ${{ number_format($regularPrice, 2) }}
                                                    </span>

                                                    <span class="rounded-full bg-red-50 px-2 py-1 text-xs font-semibold text-red-600">
                                                        Sale
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- Stock --}}
                                            <div class="mt-2">
                                                @if ($stock > 5)
                                                    <p class="flex items-center gap-1.5 text-xs font-medium text-green-700">
                                                        <span class="h-2 w-2 rounded-full bg-green-500"></span>
                                                        In stock
                                                    </p>
                                                @elseif ($stock > 0)
                                                    <p class="flex items-center gap-1.5 text-xs font-medium text-amber-700">
                                                        <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                                                        Only {{ $stock }} left
                                                    </p>
                                                @else
                                                    <p class="flex items-center gap-1.5 text-xs font-medium text-red-700">
                                                        <span class="h-2 w-2 rounded-full bg-red-500"></span>
                                                        Out of stock
                                                    </p>
                                                @endif
                                            </div>

                                            {{-- Quantity and line total --}}
                                            <div class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                                                <div>
                                                    <label
                                                        for="cart-quantity-{{ $product->id }}"
                                                        class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500"
                                                    >
                                                        Quantity
                                                    </label>

                                                    <div class="inline-flex h-11 items-center overflow-hidden rounded-xl border border-gray-300 bg-white shadow-sm">
                                                        <button
                                                            type="button"
                                                            data-cart-decrease
                                                            class="flex h-full w-11 items-center justify-center text-gray-600 transition hover:bg-gray-50 hover:text-gray-900 disabled:cursor-not-allowed disabled:opacity-40"
                                                            aria-label="Decrease quantity of {{ $product->name }}"
                                                        >
                                                            <svg
                                                                class="h-4 w-4"
                                                                viewBox="0 0 20 20"
                                                                fill="currentColor"
                                                                aria-hidden="true"
                                                            >
                                                                <path d="M4 9.25a.75.75 0 0 0 0 1.5h12a.75.75 0 0 0 0-1.5H4Z" />
                                                            </svg>
                                                        </button>

                                                        <input
                                                            id="cart-quantity-{{ $product->id }}"
                                                            type="number"
                                                            min="0"
                                                            max="{{ $stock }}"
                                                            value="{{ $quantity }}"
                                                            inputmode="numeric"
                                                            autocomplete="off"
                                                            data-cart-qty
                                                            data-product-id="{{ $product->id }}"
                                                            data-unit-price="{{ $unitPrice }}"
                                                            data-max-stock="{{ $stock }}"
                                                            class="h-full w-14 appearance-none border-x border-y-0 border-gray-300 p-0 text-center text-sm font-bold text-gray-900 focus:border-indigo-500 focus:ring-0"
                                                            aria-label="Quantity of {{ $product->name }}"
                                                        >

                                                        <button
                                                            type="button"
                                                            data-cart-increase
                                                            class="flex h-full w-11 items-center justify-center text-gray-600 transition hover:bg-gray-50 hover:text-gray-900 disabled:cursor-not-allowed disabled:opacity-40"
                                                            aria-label="Increase quantity of {{ $product->name }}"
                                                        >
                                                            <svg
                                                                class="h-4 w-4"
                                                                viewBox="0 0 20 20"
                                                                fill="currentColor"
                                                                aria-hidden="true"
                                                            >
                                                                <path d="M10.75 4a.75.75 0 0 0-1.5 0v5.25H4a.75.75 0 0 0 0 1.5h5.25V16a.75.75 0 0 0 1.5 0v-5.25H16a.75.75 0 0 0 0-1.5h-5.25V4Z" />
                                                            </svg>
                                                        </button>
                                                    </div>

                                                    <p
                                                        data-cart-status
                                                        class="mt-2 min-h-4 text-xs text-gray-500"
                                                        aria-live="polite"
                                                    ></p>
                                                </div>

                                                <div class="text-left sm:text-right">
                                                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                                        Item total
                                                    </p>

                                                    <p class="mt-1 text-lg font-bold text-gray-900">
                                                        $<span id="line-total-{{ $product->id }}">{{ number_format($lineTotal, 2) }}</span>
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Mobile remove button --}}
                                            <form
                                                action="{{ route('cart.remove', $product) }}"
                                                method="POST"
                                                class="mt-4 sm:hidden"
                                                onsubmit="return confirm('Remove this product from your cart?');"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center gap-2 text-sm font-semibold text-red-600 transition hover:text-red-700"
                                                >
                                                    <svg
                                                        class="h-4 w-4"
                                                        viewBox="0 0 20 20"
                                                        fill="currentColor"
                                                        aria-hidden="true"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M8.75 1.75a.75.75 0 0 0-.75.75V3H4.5a.75.75 0 0 0 0 1.5h.443l.77 12.313A1.75 1.75 0 0 0 7.46 18.5h5.08a1.75 1.75 0 0 0 1.747-1.687l.77-12.313h.443a.75.75 0 0 0 0-1.5H12v-.5a.75.75 0 0 0-.75-.75h-2.5ZM8.5 6.75a.75.75 0 0 0-1.5 0v7.5a.75.75 0 0 0 1.5 0v-7.5Zm4.5 0a.75.75 0 0 0-1.5 0v7.5a.75.75 0 0 0 1.5 0v-7.5Z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>

                                                    Remove product
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <div class="border-t border-gray-100 bg-gray-50 px-5 py-4 sm:px-6">
                            <a
                                href="{{ route('products.index') }}"
                                class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 transition hover:text-indigo-700"
                            >
                                <svg
                                    class="h-4 w-4"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M16.25 10a.75.75 0 0 1-.75.75H5.81l3.47 3.47a.75.75 0 1 1-1.06 1.06l-4.75-4.75a.75.75 0 0 1 0-1.06l4.75-4.75a.75.75 0 1 1 1.06 1.06L5.81 9.25h9.69a.75.75 0 0 1 .75.75Z"
                                        clip-rule="evenodd"
                                    />
                                </svg>

                                Continue shopping
                            </a>
                        </div>
                    </section>

                    {{-- Order summary --}}
                    <aside
                        class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6 lg:sticky lg:top-24"
                        aria-labelledby="order-summary-heading"
                    >
                        <h2
                            id="order-summary-heading"
                            class="text-xl font-bold text-gray-900"
                        >
                            Order summary
                        </h2>

                        <div class="mt-6 space-y-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">
                                    Subtotal
                                </span>

                                <span class="font-semibold text-gray-900">
                                    $<span id="cart-subtotal">{{ number_format($subtotal, 2) }}</span>
                                </span>
                            </div>

                            <div class="flex items-center justify-between gap-4 text-sm">
                                <span class="text-gray-600">
                                    Delivery
                                </span>

                                <span class="text-right font-medium text-gray-500">
                                    Calculated at checkout
                                </span>
                            </div>
                        </div>

                        <div class="my-6 border-t border-gray-200"></div>

                        <div class="flex items-end justify-between gap-4">
                            <div>
                                <p class="text-base font-bold text-gray-900">
                                    Estimated total
                                </p>

                                <p class="mt-1 text-xs text-gray-500">
                                    Excluding delivery fees
                                </p>
                            </div>

                            <p class="text-2xl font-bold text-gray-900">
                                $<span id="cart-estimated-total">{{ number_format($subtotal, 2) }}</span>
                            </p>
                        </div>

                        @auth
                            <a
                                href="{{ route('checkout.index') }}"
                                data-checkout-link
                                class="mt-7 inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Proceed to checkout

                                <svg
                                    class="ml-2 h-4 w-4"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M3.75 10a.75.75 0 0 1 .75-.75h9.69L10.72 5.78a.75.75 0 1 1 1.06-1.06l4.75 4.75a.75.75 0 0 1 0 1.06l-4.75 4.75a.75.75 0 1 1-1.06-1.06l3.47-3.47H4.5a.75.75 0 0 1-.75-.75Z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </a>
                        @else
                            <a
                                href="{{ route('login') }}"
                                data-checkout-link
                                class="mt-7 inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Sign in to checkout
                            </a>

                            <p class="mt-3 text-center text-xs leading-5 text-gray-500">
                                You need an account to complete your order.
                            </p>
                        @endauth

                        <div class="mt-6 rounded-2xl bg-gray-50 p-4">
                            <ul class="space-y-3 text-sm text-gray-600">
                                <li class="flex items-start gap-3">
                                    <svg
                                        class="mt-0.5 h-5 w-5 shrink-0 text-green-600"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M16.704 4.153a.75.75 0 0 1 .143 1.051l-7.5 10a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 1 1 1.06-1.06l3.89 3.889 6.982-9.312a.75.75 0 0 1 1.052-.143Z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>

                                    Secure order processing
                                </li>

                                <li class="flex items-start gap-3">
                                    <svg
                                        class="mt-0.5 h-5 w-5 shrink-0 text-green-600"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M16.704 4.153a.75.75 0 0 1 .143 1.051l-7.5 10a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 1 1 1.06-1.06l3.89 3.889 6.982-9.312a.75.75 0 0 1 1.052-.143Z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>

                                    Review your details before ordering
                                </li>

                                <li class="flex items-start gap-3">
                                    <svg
                                        class="mt-0.5 h-5 w-5 shrink-0 text-green-600"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M16.704 4.153a.75.75 0 0 1 .143 1.051l-7.5 10a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 1 1 1.06-1.06l3.89 3.889 6.982-9.312a.75.75 0 0 1 1.052-.143Z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>

                                    Customer support available
                                </li>
                            </ul>
                        </div>
                    </aside>
                </div>
            @endif
        </div>
    </div>
@endsection