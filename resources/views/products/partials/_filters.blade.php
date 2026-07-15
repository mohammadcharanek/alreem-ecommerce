@php
    $filterPrefix = $filterPrefix ?? 'filters';
@endphp

<form method="GET" action="{{ url()->current() }}" class="space-y-5">
    @if($q)
        <input type="hidden" name="q" value="{{ $q }}">
    @endif
    <input type="hidden" name="sort" value="{{ $sort }}">

    <details open class="border-b border-gray-200 pb-5">
        <summary class="cursor-pointer list-none text-sm font-bold text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-blue">
            Categories
        </summary>
        <div class="mt-3 max-h-56 space-y-1 overflow-y-auto pr-1">
            @forelse($categoryOptions as $option)
                @php
                    $categoryInputId = $filterPrefix . '-category-' . $option['id'];
                    $depthClass = match (min((int) $option['depth'], 3)) {
                        1 => 'pl-4',
                        2 => 'pl-8',
                        3 => 'pl-10',
                        default => '',
                    };
                @endphp
                <label for="{{ $categoryInputId }}"
                       class="{{ $depthClass }} flex min-h-9 cursor-pointer items-center gap-2 rounded-lg px-2 py-1.5 text-sm text-gray-700 hover:bg-gray-50">
                    <input
                        id="{{ $categoryInputId }}"
                        type="checkbox"
                        name="category[]"
                        value="{{ $option['id'] }}"
                        @checked(in_array((int) $option['id'], $selectedCategoryIds, true))
                        class="h-4 w-4 rounded border-gray-300 text-brand-blue focus:ring-brand-blue"
                    >
                    <span class="min-w-0 truncate">{{ $option['name'] }}</span>
                </label>
            @empty
                <p class="text-sm text-gray-500">No categories available.</p>
            @endforelse
        </div>
    </details>

    <details {{ count($selectedBrandIds) ? 'open' : '' }} class="border-b border-gray-200 pb-5">
        <summary class="cursor-pointer list-none text-sm font-bold text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-blue">
            Brands
        </summary>
        <div class="mt-3 max-h-48 space-y-1 overflow-y-auto pr-1">
            @forelse($brands as $brandOption)
                @php $brandInputId = $filterPrefix . '-brand-' . $brandOption->id; @endphp
                <label for="{{ $brandInputId }}" class="flex min-h-9 cursor-pointer items-center gap-2 rounded-lg px-2 py-1.5 text-sm text-gray-700 hover:bg-gray-50">
                    <input
                        id="{{ $brandInputId }}"
                        type="checkbox"
                        name="brand[]"
                        value="{{ $brandOption->id }}"
                        @checked(in_array((int) $brandOption->id, $selectedBrandIds, true))
                        class="h-4 w-4 rounded border-gray-300 text-brand-blue focus:ring-brand-blue"
                    >
                    <span class="min-w-0 truncate">{{ $brandOption->name }}</span>
                </label>
            @empty
                <p class="text-sm text-gray-500">No brands available.</p>
            @endforelse
        </div>
    </details>

    <details {{ count($selectedVendorIds) ? 'open' : '' }} class="border-b border-gray-200 pb-5">
        <summary class="cursor-pointer list-none text-sm font-bold text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-blue">
            Vendors
        </summary>
        <div class="mt-3 max-h-48 space-y-1 overflow-y-auto pr-1">
            @forelse($vendors as $vendorOption)
                @php $vendorInputId = $filterPrefix . '-vendor-' . $vendorOption->id; @endphp
                <label for="{{ $vendorInputId }}" class="flex min-h-9 cursor-pointer items-center gap-2 rounded-lg px-2 py-1.5 text-sm text-gray-700 hover:bg-gray-50">
                    <input
                        id="{{ $vendorInputId }}"
                        type="checkbox"
                        name="vendor[]"
                        value="{{ $vendorOption->id }}"
                        @checked(in_array((int) $vendorOption->id, $selectedVendorIds, true))
                        class="h-4 w-4 rounded border-gray-300 text-brand-blue focus:ring-brand-blue"
                    >
                    <span class="min-w-0 truncate">{{ $vendorOption->name }}</span>
                </label>
            @empty
                <p class="text-sm text-gray-500">No vendors available.</p>
            @endforelse
        </div>
    </details>

    <fieldset class="border-b border-gray-200 pb-5">
        <legend class="text-sm font-bold text-gray-900">Availability</legend>
        <div class="mt-3 space-y-2">
            @foreach([
                '' => 'All products',
                'in_stock' => 'In stock',
                'out_of_stock' => 'Out of stock',
            ] as $stockValue => $stockLabel)
                @php $stockInputId = $filterPrefix . '-stock-' . ($stockValue ?: 'all'); @endphp
                <label for="{{ $stockInputId }}" class="flex min-h-9 cursor-pointer items-center gap-2 rounded-lg px-2 py-1.5 text-sm text-gray-700 hover:bg-gray-50">
                    <input
                        id="{{ $stockInputId }}"
                        type="radio"
                        name="stock"
                        value="{{ $stockValue }}"
                        @checked($stockFilter === $stockValue)
                        class="h-4 w-4 border-gray-300 text-brand-blue focus:ring-brand-blue"
                    >
                    <span>{{ $stockLabel }}</span>
                </label>
            @endforeach
        </div>
    </fieldset>

    <fieldset>
        <legend class="text-sm font-bold text-gray-900">Price range</legend>
        <div class="mt-3 grid grid-cols-2 gap-3">
            <div>
                <label for="{{ $filterPrefix }}-min-price" class="mb-1 block text-xs font-medium text-gray-600">Minimum</label>
                <input
                    id="{{ $filterPrefix }}-min-price"
                    type="number"
                    name="min_price"
                    value="{{ $minPrice }}"
                    min="0"
                    step="0.01"
                    inputmode="decimal"
                    placeholder="$0"
                    class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm focus:border-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue/20"
                >
            </div>
            <div>
                <label for="{{ $filterPrefix }}-max-price" class="mb-1 block text-xs font-medium text-gray-600">Maximum</label>
                <input
                    id="{{ $filterPrefix }}-max-price"
                    type="number"
                    name="max_price"
                    value="{{ $maxPrice }}"
                    min="0"
                    step="0.01"
                    inputmode="decimal"
                    placeholder="Any"
                    class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm focus:border-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue/20"
                >
            </div>
        </div>
    </fieldset>

    <div class="grid grid-cols-2 gap-3 pt-1">
        <button type="submit"
                class="inline-flex min-h-11 items-center justify-center rounded-xl bg-brand-blue px-4 text-sm font-bold text-white hover:brightness-95 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2">
            Apply filters
        </button>
        <a href="{{ $clearFiltersUrl }}"
           class="inline-flex min-h-11 items-center justify-center rounded-xl border border-gray-300 bg-white px-4 text-sm font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2">
            Clear
        </a>
    </div>
</form>
