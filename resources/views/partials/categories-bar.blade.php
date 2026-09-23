@php
    // Use composer-provided navigation categories and fall back safely.
    $raw = $navCategories ?? $categories ?? collect();

    // Top-level categories only.
    $parents = collect($raw)
        ->filter(fn ($category) => ($category->parent_id ?? null) === null)
        ->values();

    // Keep the desktop bar compact. Overflow goes into "More".
    $top = $parents->take(6)->values();
    $more = $parents->slice(6)->values();
@endphp

@if($parents->count())
    <nav
        x-data="catbar()"
        class="relative z-[60] border-b border-slate-200 bg-white"
        aria-label="Product categories"
    >
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Desktop category navigation --}}
            <div class="hidden h-12 items-center gap-2 md:flex">
                <a
                    href="{{ route('products.index') }}"
                    class="inline-flex shrink-0 items-center gap-2 rounded-lg bg-slate-950 px-3 py-2 text-xs font-bold uppercase tracking-[0.12em] text-white transition hover:bg-blue-700"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                        <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                        <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                        <rect x="14" y="14" width="7" height="7" rx="1"></rect>
                    </svg>
                    Shop categories
                </a>

                <span class="h-5 w-px shrink-0 bg-slate-200" aria-hidden="true"></span>

                <div class="flex min-w-0 flex-1 items-center gap-1">
                    @foreach($top as $parent)
                        @php $idx = $loop->index; @endphp

                        <div
                            class="relative"
                            @mouseenter="openMenu({{ $idx }})"
                            @mouseleave="closeMenuDelayed({{ $idx }})"
                            @focusin="openMenu({{ $idx }})"
                            @focusout="closeMenuDelayed({{ $idx }})"
                        >
                            <a
                                href="{{ route('products.byCategory', ['slug' => $parent->slug]) }}"
                                class="inline-flex max-w-[11rem] items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-blue-50 hover:text-blue-700"
                                role="menuitem"
                                aria-haspopup="{{ $parent->children->count() ? 'true' : 'false' }}"
                            >
                                <span class="truncate">{{ $parent->name }}</span>

                                @if($parent->children->count())
                                    <svg class="h-3.5 w-3.5 shrink-0 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.128l3.71-3.9a.75.75 0 1 1 1.08 1.04l-4.24 4.46a.75.75 0 0 1-1.08 0l-4.24-4.46a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </a>

                            @if($parent->children->count())
                                <div
                                    x-show="isOpen({{ $idx }})"
                                    x-cloak
                                    x-transition.origin.top.left
                                    @mouseenter="cancelClose({{ $idx }})"
                                    @mouseleave="closeMenu({{ $idx }})"
                                    class="absolute left-0 top-full z-[80] mt-2 w-[32rem] overflow-hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-2xl"
                                >
                                    <div class="mb-3 flex items-center justify-between border-b border-slate-100 pb-3">
                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-[0.14em] text-blue-700">
                                                {{ $parent->name }}
                                            </p>
                                            <p class="mt-1 text-xs text-slate-500">
                                                Browse subcategories
                                            </p>
                                        </div>

                                        <a
                                            href="{{ route('products.byCategory', ['slug' => $parent->slug]) }}"
                                            class="text-xs font-bold text-blue-700 hover:text-blue-900"
                                        >
                                            View all
                                        </a>
                                    </div>

                                    <div class="grid max-h-80 grid-cols-2 gap-x-6 gap-y-4 overflow-y-auto pr-1">
                                        @foreach($parent->children as $child)
                                            <div class="min-w-0">
                                                <a
                                                    href="{{ route('products.byCategory', ['slug' => $child->slug]) }}"
                                                    class="block truncate text-sm font-bold text-slate-900 transition hover:text-blue-700"
                                                >
                                                    {{ $child->name }}
                                                </a>

                                                @if($child->children->count())
                                                    <ul class="mt-2 space-y-1.5">
                                                        @foreach($child->children as $sub)
                                                            <li>
                                                                <a
                                                                    href="{{ route('products.byCategory', ['slug' => $sub->slug]) }}"
                                                                    class="block truncate text-sm text-slate-500 transition hover:text-blue-700"
                                                                >
                                                                    {{ $sub->name }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    @if($more->count())
                        <div
                            class="relative"
                            @mouseenter="openMenu('more')"
                            @mouseleave="closeMenuDelayed('more')"
                            @focusin="openMenu('more')"
                            @focusout="closeMenuDelayed('more')"
                        >
                            <button
                                type="button"
                                class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-blue-50 hover:text-blue-700"
                            >
                                More
                                <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.128l3.71-3.9a.75.75 0 1 1 1.08 1.04l-4.24 4.46a.75.75 0 0 1-1.08 0l-4.24-4.46a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"></path>
                                </svg>
                            </button>

                            <div
                                x-show="isOpen('more')"
                                x-cloak
                                x-transition.origin.top.left
                                @mouseenter="cancelClose('more')"
                                @mouseleave="closeMenu('more')"
                                class="absolute left-0 top-full z-[80] mt-2 w-72 rounded-2xl border border-slate-200 bg-white p-2 shadow-2xl"
                            >
                                <div class="max-h-80 overflow-y-auto">
                                    @foreach($more as $parent)
                                        <a
                                            href="{{ route('products.byCategory', ['slug' => $parent->slug]) }}"
                                            class="flex items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-blue-50 hover:text-blue-700"
                                        >
                                            <span class="truncate">{{ $parent->name }}</span>

                                            @if($parent->children->count())
                                                <span class="text-xs text-slate-400">
                                                    {{ $parent->children->count() }}
                                                </span>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <a
                    href="{{ route('products.index') }}"
                    class="ml-auto inline-flex shrink-0 items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-bold text-blue-700 transition hover:bg-blue-50 hover:text-blue-900"
                >
                    View all
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>
                </a>
            </div>

            {{-- Mobile category navigation --}}
            <div class="flex min-h-12 items-center justify-between gap-3 md:hidden">
                <button
                    type="button"
                    @click="mobileOpen = !mobileOpen"
                    :aria-expanded="mobileOpen.toString()"
                    class="inline-flex min-h-10 items-center gap-2 rounded-lg text-sm font-bold text-slate-800"
                >
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-950 text-white">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                            <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                            <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                            <rect x="14" y="14" width="7" height="7" rx="1"></rect>
                        </svg>
                    </span>

                    Shop categories

                    <svg class="h-4 w-4 text-slate-400 transition" :class="{ 'rotate-180': mobileOpen }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.128l3.71-3.9a.75.75 0 1 1 1.08 1.04l-4.24 4.46a.75.75 0 0 1-1.08 0l-4.24-4.46a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"></path>
                    </svg>
                </button>

                <a
                    href="{{ route('products.index') }}"
                    class="text-xs font-bold text-blue-700"
                >
                    View all
                </a>
            </div>
        </div>

        {{-- Mobile category accordion --}}
        <div
            x-show="mobileOpen"
            x-cloak
            x-transition
            @click.outside="mobileOpen = false"
            class="border-t border-slate-200 bg-white md:hidden"
        >
            <div class="mx-auto max-h-[70vh] max-w-7xl overflow-y-auto px-4 py-3 sm:px-6">
                @foreach($parents as $parent)
                    <div x-data="{ open: false }" class="border-b border-slate-100 last:border-b-0">
                        <div class="flex items-center gap-2">
                            <a
                                href="{{ route('products.byCategory', ['slug' => $parent->slug]) }}"
                                class="flex min-h-12 min-w-0 flex-1 items-center font-semibold text-slate-800"
                            >
                                <span class="truncate">{{ $parent->name }}</span>
                            </a>

                            @if($parent->children->count())
                                <button
                                    type="button"
                                    @click="open = !open"
                                    :aria-expanded="open.toString()"
                                    class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-50"
                                    aria-label="Toggle {{ $parent->name }} subcategories"
                                >
                                    <svg class="h-4 w-4 transition" :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.128l3.71-3.9a.75.75 0 1 1 1.08 1.04l-4.24 4.46a.75.75 0 0 1-1.08 0l-4.24-4.46a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>

                        @if($parent->children->count())
                            <div x-show="open" x-cloak class="pb-3 pl-3">
                                @foreach($parent->children as $child)
                                    <div x-data="{ childOpen: false }">
                                        <div class="flex items-center gap-2">
                                            <a
                                                href="{{ route('products.byCategory', ['slug' => $child->slug]) }}"
                                                class="flex min-h-10 min-w-0 flex-1 items-center text-sm font-semibold text-slate-700"
                                            >
                                                <span class="truncate">{{ $child->name }}</span>
                                            </a>

                                            @if($child->children->count())
                                                <button
                                                    type="button"
                                                    @click="childOpen = !childOpen"
                                                    :aria-expanded="childOpen.toString()"
                                                    class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400"
                                                    aria-label="Toggle {{ $child->name }} subcategories"
                                                >
                                                    <svg class="h-3.5 w-3.5 transition" :class="{ 'rotate-180': childOpen }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.128l3.71-3.9a.75.75 0 1 1 1.08 1.04l-4.24 4.46a.75.75 0 0 1-1.08 0l-4.24-4.46a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>

                                        @if($child->children->count())
                                            <div x-show="childOpen" x-cloak class="space-y-1 border-l border-slate-200 pb-2 pl-3">
                                                @foreach($child->children as $sub)
                                                    <a
                                                        href="{{ route('products.byCategory', ['slug' => $sub->slug]) }}"
                                                        class="block py-1.5 text-sm text-slate-500 transition hover:text-blue-700"
                                                    >
                                                        {{ $sub->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </nav>

    <script>
        function catbar() {
            return {
                openId: null,
                mobileOpen: false,
                timers: new Map(),

                isOpen(id) {
                    return this.openId === id;
                },

                openMenu(id) {
                    this.clearTimer(id);
                    this.openId = id;
                },

                closeMenu(id) {
                    this.clearTimer(id);

                    if (this.openId === id) {
                        this.openId = null;
                    }
                },

                closeMenuDelayed(id) {
                    this.clearTimer(id);

                    const timer = setTimeout(() => {
                        if (this.openId === id) {
                            this.openId = null;
                        }
                    }, 160);

                    this.timers.set(id, timer);
                },

                cancelClose(id) {
                    this.clearTimer(id);
                },

                clearTimer(id) {
                    const timer = this.timers.get(id);

                    if (timer) {
                        clearTimeout(timer);
                        this.timers.delete(id);
                    }
                }
            };
        }
    </script>
@endif
