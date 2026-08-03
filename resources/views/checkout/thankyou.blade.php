@extends('layouts.app')

@section('title', 'Thank You')

@push('meta')
    <meta name="robots" content="noindex,nofollow">
@endpush

@section('content')
<div class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
    <div class="rounded-2xl bg-white p-6 text-center shadow-sm ring-1 ring-gray-200 sm:p-8">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-green-600">
            <svg
                class="h-9 w-9"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                aria-hidden="true"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="m5 12 4 4L19 6"
                />
            </svg>
        </div>

        <h1 class="mt-5 text-3xl font-bold text-gray-900">
            Thank you for your order
        </h1>

        <p class="mt-2 text-gray-600">
            Your order
            <span class="font-semibold text-gray-900">
                #{{ $order->id }}
            </span>
            was placed successfully.
        </p>

        <div class="mt-8 overflow-hidden rounded-xl border border-gray-200 text-left">
            <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                <h2 class="font-semibold text-gray-900">
                    Order summary
                </h2>
            </div>

            <ul class="divide-y divide-gray-200">
                @forelse($order->items as $item)
                    <li class="flex items-start justify-between gap-4 px-4 py-4">
                        <div>
                            <p class="font-medium text-gray-900">
                                {{ $item->product?->name ?? ('Product #' . $item->product_id) }}
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                Quantity: {{ (int) $item->quantity }}
                                ×
                                ${{ number_format((float) $item->price, 2) }}
                            </p>
                        </div>

                        <span class="shrink-0 font-semibold text-gray-900">
                            ${{ number_format(
                                (float) $item->price * (int) $item->quantity,
                                2
                            ) }}
                        </span>
                    </li>
                @empty
                    <li class="px-4 py-6 text-center text-gray-500">
                        No order items were found.
                    </li>
                @endforelse
            </ul>

            <div class="flex items-center justify-between border-t border-gray-200 bg-gray-50 px-4 py-4">
                <span class="text-lg font-semibold text-gray-900">
                    Total
                </span>

                <span class="text-xl font-bold text-gray-900">
                    ${{ number_format((float) $order->total_amount, 2) }}
                </span>
            </div>
        </div>

        <div class="mt-6 rounded-xl bg-gray-50 p-4 text-left text-sm">
            <div class="flex justify-between gap-4">
                <span class="text-gray-500">Status</span>
                <span class="font-medium text-gray-900">
                    {{ ucfirst((string) $order->status) }}
                </span>
            </div>

            <div class="mt-2 flex justify-between gap-4">
                <span class="text-gray-500">Payment</span>
                <span class="font-medium text-gray-900">
                    {{ \Illuminate\Support\Str::headline((string) $order->payment_method) }}
                </span>
            </div>

            @if($order->voucher_code)
                <div class="mt-2 flex justify-between gap-4">
                    <span class="text-gray-500">Voucher</span>
                    <span class="font-mono font-medium text-gray-900">
                        {{ $order->voucher_code }}
                    </span>
                </div>
            @endif
        </div>

        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a
                href="{{ route('products.index') }}"
                class="rounded-lg bg-indigo-600 px-5 py-2.5 font-medium text-white transition hover:bg-indigo-700"
            >
                Continue shopping
            </a>

            @auth
                <a
                    href="{{ route('orders.index') }}"
                    class="rounded-lg bg-gray-100 px-5 py-2.5 font-medium text-gray-900 transition hover:bg-gray-200"
                >
                    View my orders
                </a>
            @endauth
        </div>

        @if(session('success'))
            <p class="mt-5 text-sm text-green-700">
                {{ session('success') }}
            </p>
        @endif
    </div>
</div>
@endsection