@extends('layouts.app')

@php
    $contextName = $category->name ?? $brand->name ?? $vendor->name ?? null;

    $pageHeading = isset($category)
        ? "Products in {$category->name}"
        : (isset($brand)
            ? "Products by {$brand->name}"
            : (isset($vendor)
                ? "Products from {$vendor->name}"
                : 'Shop All Products'));

    $pageIntro = isset($category)
        ? "Browse available products in the {$category->name} category."
        : (isset($brand)
            ? "Explore products supplied by {$brand->name}."
            : (isset($vendor)
                ? "Browse products available from {$vendor->name}."
                : 'Discover imported products selected for quality, value, and dependable everyday use.'));

    $currentPath = url()->current();

    $makeQueryUrl = static function (array $query) use ($currentPath): string {
        unset($query['page']);
        $query = array_filter($query, static fn ($value) => $value !== null && $value !== '' && $value !== []);

        return $currentPath . ($query ? '?' . http_build_query($query) : '');
    };

    $removeFilterUrl = static function (string $key, mixed $value = null) use ($makeQueryUrl): string {
        $query = request()->query();
        unset($query['page']);

        if ($value === null) {
            unset($query[$key]);
        } else {
            $currentValues = array_values(array_filter(
                (array) ($query[$key] ?? []),
                static fn ($item) => (string) $item !== (string) $value
            ));

            if ($currentValues) {
                $query[$key] = $currentValues;
            } else {
                unset($query[$key]);
            }
        }

        return $makeQueryUrl($query);
    };

    $searchClearUrl = $removeFilterUrl('q');
    $hasActiveFilters = ($activeFilterCount ?? 0) > 0;
@endphp

@section('title', $pageHeading . ' | Al Reem Expo')
@section('meta_description', $pageIntro)
@section('canonical', url()->current())
@section('robots', count(request()->except('page')) > 0 ? 'noindex,follow' : 'index,follow')

@section('content')
<div class="space-y-6">
    {{-- Breadcrumbs --}}
    <nav aria-label="Breadcrumb" class="text-sm text-gray-500">
        <ol class="flex flex-wrap items-center gap-2">
            <li>
                <a href="{{ route('home') }}" class="rounded hover:text-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2">
                    Home
                </a>
            </li>
            <li aria-hidden="true" class="text-gray-300">/</li>
            <li>
                @if($contextName)
                    <a href="{{ route('products.index') }}" class="rounded hover:text-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2">
                        Products
                    </a>
                @else
                    <span aria-current="page" class="font-medium text-gray-700">Products</span>
                @endif
            </li>
            @if($contextName)
                <li aria-hidden="true" class="text-gray-300">/</li>
                <li aria-current="page" class="font-medium text-gray-700">{{ $contextName }}</li>
            @endif
        </ol>
    </nav>

    {{-- Page heading --}}
    <header class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div class="max-w-3xl">
            <h1 class="text-2xl font-bold tracking-tight text-gray-950 sm:text-3xl">
                {{ $pageHeading }}
            </h1>
            <p class="mt-2 text-sm leading-6 text-gray-600 sm:text-base">
                {{ $pageIntro }}
            </p>
        </div>

        <p class="shrink-0 text-sm font-medium text-gray-600" aria-live="polite">
            {{ number_format($products->total()) }} {{ \Illuminate\Support\Str::plural('product', $products->total()) }}
        </p>
    </header>

    {{-- Search and sort toolbar --}}
    <form method="GET" action="{{ url()->current() }}" class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
        @foreach($selectedCategoryIds as $categoryId)
            <input type="hidden" name="category[]" value="{{ $categoryId }}">
        @endforeach
        @foreach($selectedBrandIds as $brandId)
            <input type="hidden" name="brand[]" value="{{ $brandId }}">
        @endforeach
        @foreach($selectedVendorIds as $vendorId)
            <input type="hidden" name="vendor[]" value="{{ $vendorId }}">
        @endforeach
        @if($stockFilter)
            <input type="hidden" name="stock" value="{{ $stockFilter }}">
        @endif
        @if($minPrice !== null)
            <input type="hidden" name="min_price" value="{{ $minPrice }}">
        @endif
        @if($maxPrice !== null)
            <input type="hidden" name="max_price" value="{{ $maxPrice }}">
        @endif

        <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_220px_auto] md:items-end">
            <div>
                <label for="product-search" class="mb-1.5 block text-sm font-semibold text-gray-800">
                    Search products
                </label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400" aria-hidden="true">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-3.5-3.5"></path>
                        </svg>
                    </span>
                    <input
                        id="product-search"
                        type="search"
                        name="q"
                        value="{{ $q }}"
                        placeholder="Search by product name, SKU, or description"
                        autocomplete="off"
                        class="h-11 w-full rounded-xl border border-gray-300 bg-white py-2 pl-10 pr-24 text-sm text-gray-900 placeholder:text-gray-400 focus:border-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue/20"
                    >
                    @if($q)
                        <a href="{{ $searchClearUrl }}"
                           class="absolute inset-y-0 right-2 my-auto inline-flex h-8 items-center rounded-lg px-2 text-xs font-semibold text-gray-500 hover:bg-gray-100 hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-brand-blue"
                           aria-label="Clear product search">
                            Clear
                        </a>
                    @endif
                </div>
            </div>

            <div>
                <label for="product-sort" class="mb-1.5 block text-sm font-semibold text-gray-800">
                    Sort by
                </label>
                <select
                    id="product-sort"
                    name="sort"
                    class="h-11 w-full rounded-xl border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue/20"
                >
                    <option value="recommended" @selected($sort === 'recommended')>Recommended</option>
                    <option value="latest" @selected($sort === 'latest')>Newest</option>
                    <option value="price_asc" @selected($sort === 'price_asc')>Price: low to high</option>
                    <option value="price_desc" @selected($sort === 'price_desc')>Price: high to low</option>
                    <option value="name_asc" @selected($sort === 'name_asc')>Name: A to Z</option>
                    <option value="name_desc" @selected($sort === 'name_desc')>Name: Z to A</option>
                    <option value="rating_desc" @selected($sort === 'rating_desc')>Best rated</option>
                </select>
            </div>

            <button type="submit"
                    class="inline-flex h-11 items-center justify-center rounded-xl bg-brand-blue px-5 text-sm font-bold text-white shadow-sm transition hover:brightness-95 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2">
                Search
            </button>
        </div>
    </form>

    {{-- Mobile filters: native and usable without JavaScript --}}
    <details class="group rounded-2xl border border-gray-200 bg-white shadow-sm lg:hidden">
        <summary class="flex min-h-12 cursor-pointer list-none items-center justify-between gap-3 px-4 py-3 font-semibold text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-blue">
            <span class="inline-flex items-center gap-2">
                <svg class="h-5 w-5 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M4 6h16M7 12h10M10 18h4"></path>
                </svg>
                Filters
                @if(($activeFilterCount ?? 0) > 0)
                    <span class="inline-flex min-w-6 items-center justify-center rounded-full bg-brand-blue px-1.5 py-0.5 text-xs font-bold text-white">
                        {{ $activeFilterCount }}
                    </span>
                @endif
            </span>
            <svg class="h-5 w-5 transition group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="m6 9 6 6 6-6"></path>
            </svg>
        </summary>
        <div class="border-t border-gray-200 p-4">
            @include('products.partials._filters', ['filterPrefix' => 'mobile'])
        </div>
    </details>

    {{-- Active filter chips --}}
    @if($hasActiveFilters)
        <section aria-label="Active filters" class="rounded-2xl border border-sky-100 bg-sky-50/70 p-4">
            <div class="flex flex-wrap items-center gap-2">
                <span class="mr-1 text-sm font-semibold text-gray-800">Active filters:</span>

                @if($q)
                    <a href="{{ $removeFilterUrl('q') }}"
                       class="inline-flex items-center gap-1 rounded-full border border-sky-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:border-brand-blue hover:text-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue">
                        Search: “{{ $q }}” <span aria-hidden="true">×</span>
                        <span class="sr-only">Remove search filter</span>
                    </a>
                @endif

                @foreach($selectedCategoryIds as $categoryId)
                    <a href="{{ $removeFilterUrl('category', $categoryId) }}"
                       class="inline-flex items-center gap-1 rounded-full border border-sky-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:border-brand-blue hover:text-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue">
                        {{ $filterLabels['categories'][$categoryId] ?? 'Category' }} <span aria-hidden="true">×</span>
                        <span class="sr-only">Remove category filter</span>
                    </a>
                @endforeach

                @foreach($selectedBrandIds as $brandId)
                    <a href="{{ $removeFilterUrl('brand', $brandId) }}"
                       class="inline-flex items-center gap-1 rounded-full border border-sky-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:border-brand-blue hover:text-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue">
                        {{ $filterLabels['brands'][$brandId] ?? 'Brand' }} <span aria-hidden="true">×</span>
                        <span class="sr-only">Remove brand filter</span>
                    </a>
                @endforeach

                @foreach($selectedVendorIds as $vendorId)
                    <a href="{{ $removeFilterUrl('vendor', $vendorId) }}"
                       class="inline-flex items-center gap-1 rounded-full border border-sky-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:border-brand-blue hover:text-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue">
                        {{ $filterLabels['vendors'][$vendorId] ?? 'Vendor' }} <span aria-hidden="true">×</span>
                        <span class="sr-only">Remove vendor filter</span>
                    </a>
                @endforeach

                @if($stockFilter)
                    <a href="{{ $removeFilterUrl('stock') }}"
                       class="inline-flex items-center gap-1 rounded-full border border-sky-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:border-brand-blue hover:text-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue">
                        {{ $stockFilter === 'in_stock' ? 'In stock' : 'Out of stock' }} <span aria-hidden="true">×</span>
                        <span class="sr-only">Remove availability filter</span>
                    </a>
                @endif

                @if($minPrice !== null || $maxPrice !== null)
                    <a href="{{ $makeQueryUrl(array_diff_key(request()->query(), array_flip(['min_price', 'max_price']))) }}"
                       class="inline-flex items-center gap-1 rounded-full border border-sky-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:border-brand-blue hover:text-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue">
                        Price:
                        @if($minPrice !== null && $maxPrice !== null)
                            ${{ number_format($minPrice, 2) }}–${{ number_format($maxPrice, 2) }}
                        @elseif($minPrice !== null)
                            from ${{ number_format($minPrice, 2) }}
                        @else
                            up to ${{ number_format($maxPrice, 2) }}
                        @endif
                        <span aria-hidden="true">×</span>
                        <span class="sr-only">Remove price filter</span>
                    </a>
                @endif

                <a href="{{ $clearFiltersUrl }}"
                   class="ml-auto text-sm font-bold text-brand-blue hover:underline focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2">
                    Clear all
                </a>
            </div>
        </section>
    @endif

    <div class="grid items-start gap-6 lg:grid-cols-[260px_minmax(0,1fr)]">
        {{-- Desktop filters --}}
        <aside class="sticky top-24 hidden max-h-[calc(100vh-7rem)] overflow-y-auto rounded-2xl border border-gray-200 bg-white p-5 shadow-sm lg:block" aria-label="Product filters">
            <div class="mb-4 flex items-center justify-between gap-3">
                <h2 class="text-base font-bold text-gray-950">Filters</h2>
                @if(($activeFilterCount ?? 0) > 0)
                    <span class="rounded-full bg-sky-100 px-2 py-1 text-xs font-bold text-sky-800">
                        {{ $activeFilterCount }} selected
                    </span>
                @endif
            </div>
            @include('products.partials._filters', ['filterPrefix' => 'desktop'])
        </aside>

        {{-- Results --}}
        <section aria-labelledby="product-results-heading" class="min-w-0">
            <div class="mb-4 flex items-center justify-between gap-3">
                <h2 id="product-results-heading" class="text-base font-bold text-gray-950">
                    Product results
                </h2>
                @if($products->total() > 0)
                    <p class="text-xs text-gray-500 sm:text-sm">
                        Showing {{ number_format($products->firstItem()) }}–{{ number_format($products->lastItem()) }}
                        of {{ number_format($products->total()) }}
                    </p>
                @endif
            </div>

            @if($products->count())
                <div class="grid grid-cols-1 gap-4 min-[360px]:grid-cols-2 md:grid-cols-3 xl:grid-cols-4">
                    @foreach($products as $product)
                        @include('products.partials._card', ['product' => $product])
                    @endforeach
                </div>

                @if($products->hasPages())
                    <nav class="mt-8 border-t border-gray-200 pt-6" aria-label="Product pagination">
                        {{ $products->links() }}
                    </nav>
                @endif
            @else
                <div class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-14 text-center shadow-sm">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-sky-50 text-brand-blue" aria-hidden="true">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-3.5-3.5M8.5 11h5"></path>
                        </svg>
                    </div>
                    <h2 class="mt-4 text-xl font-bold text-gray-950">No matching products found</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-600">
                        Try removing one or more filters, using a broader search term, or returning to the full product collection.
                    </p>
                    <div class="mt-6 flex flex-col justify-center gap-3 sm:flex-row">
                        <a href="{{ $clearFiltersUrl }}"
                           class="inline-flex min-h-11 items-center justify-center rounded-xl bg-brand-blue px-5 text-sm font-bold text-white hover:brightness-95 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2">
                            Clear filters
                        </a>
                        <a href="{{ route('products.index') }}"
                           class="inline-flex min-h-11 items-center justify-center rounded-xl border border-gray-300 bg-white px-5 text-sm font-bold text-gray-800 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2">
                            View all products
                        </a>
                    </div>
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
