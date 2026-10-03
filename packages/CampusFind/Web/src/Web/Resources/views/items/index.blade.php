<x-campusfind_web_web::layouts>
    <x-campusfind_web_web::section class="py-10">
        <!-- Header and Search Form -->
        <div class="mb-10">
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">
                @lang('campusfind_web_web::app.web.browse_items')
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                @lang('campusfind_web_web::app.web.hero.subheadline')
            </p>

            <form action="{{ route('campusfind_web.web.items.index') }}" method="GET" class="mt-6 flex flex-col md:flex-row gap-3">
                <div class="flex-1">
                    <input
                        type="text"
                        name="query"
                        value="{{ $query ?? '' }}"
                        placeholder="@lang('campusfind_web_web::app.web.search_placeholder')"
                        class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-3 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-sm"
                    />
                </div>

                @if (! empty($categories))
                    <div class="md:w-64">
                        <select
                            name="category"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-3 text-sm text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-sm"
                        >
                            <option value="">@lang('campusfind_web_web::app.web.all_categories')</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->code }}" {{ ($category ?? '') === $cat->code ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <button
                    type="submit"
                    class="rounded-xl bg-[var(--brand-color)] px-6 py-3 text-sm font-bold text-white shadow hover:opacity-90 transition-opacity cursor-pointer border-0"
                >
                    @lang('campusfind_web_web::app.web.search')
                </button>

                @if (! empty($query) || ! empty($category))
                    <a
                        href="{{ route('campusfind_web.web.items.index') }}"
                        class="rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 text-center text-decoration-none shadow-sm"
                    >
                        ✕
                    </a>
                @endif
            </form>
        </div>

        <!-- Items Grid -->
        @if ($searchResult->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($searchResult->items as $item)
                    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden shadow-sm hover:shadow-md transition-shadow flex flex-col">
                        <div class="relative h-48 bg-gray-100 dark:bg-gray-800 flex items-center justify-center overflow-hidden">
                            @if ($item->hasImage && $item->imageUrl)
                                <img src="{{ $item->imageUrl }}" alt="{{ $item->title }}" class="h-full w-full object-cover">
                            @else
                                <div class="text-gray-400 dark:text-gray-600 flex flex-col items-center">
                                    <svg class="h-12 w-12 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-xs mt-1">@lang('campusfind_web_web::app.web.images')</span>
                                </div>
                            @endif

                            @if ($item->category)
                                <span class="absolute top-3 ltr:left-3 rtl:right-3 px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-600/90 text-white backdrop-blur">
                                    {{ $item->category }}
                                </span>
                            @endif

                            <span class="absolute bottom-3 ltr:right-3 rtl:left-3 px-2 py-0.5 text-xs font-mono font-medium rounded bg-black/60 text-white backdrop-blur">
                                {{ $item->reference }}
                            </span>
                        </div>

                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <h3 class="font-bold text-lg text-gray-900 dark:text-white line-clamp-1">
                                    {{ $item->title }}
                                </h3>

                                @if ($item->description)
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                                        {{ $item->description }}
                                    </p>
                                @endif

                                <div class="mt-4 space-y-1.5 text-xs text-gray-500 dark:text-gray-400">
                                    @if ($item->foundLocation)
                                        <div class="flex items-center gap-1.5">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span>{{ $item->foundLocation }}</span>
                                        </div>
                                    @endif

                                    @if ($item->foundAt)
                                        <div class="flex items-center gap-1.5">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span>{{ $item->foundAt->format('Y-m-d') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                                <a
                                    href="{{ route('campusfind_web.web.items.show', ['reference' => $item->reference]) }}"
                                    class="text-sm font-semibold text-[var(--brand-color)] hover:underline inline-flex items-center gap-1 text-decoration-none"
                                >
                                    <span>@lang('campusfind_web_web::app.web.view_details')</span>
                                    <svg class="h-4 w-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Bounded Pagination -->
            @if ($searchResult->hasPages())
                <div class="mt-10 flex items-center justify-center gap-2">
                    @if ($searchResult->previousPage())
                        <a
                            href="{{ route('campusfind_web.web.items.index', array_merge(request()->query(), ['page' => $searchResult->previousPage()])) }}"
                            class="px-4 py-2 text-sm font-semibold rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-50 text-decoration-none shadow-sm"
                        >
                            &laquo; Previous
                        </a>
                    @endif

                    <span class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                        {{ $searchResult->currentPage }} / {{ $searchResult->lastPage }}
                    </span>

                    @if ($searchResult->nextPage())
                        <a
                            href="{{ route('campusfind_web.web.items.index', array_merge(request()->query(), ['page' => $searchResult->nextPage()])) }}"
                            class="px-4 py-2 text-sm font-semibold rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-50 text-decoration-none shadow-sm"
                        >
                            Next &raquo;
                        </a>
                    @endif
                </div>
            @endif
        @else
            <div class="py-16 text-center bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-8">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-4 text-lg font-bold text-gray-900 dark:text-white">
                    @lang('campusfind_web_web::app.web.no_items_found')
                </h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                    @lang('campusfind_web_web::app.web.hero.subheadline')
                </p>
            </div>
        @endif
    </x-campusfind_web_web::section>
</x-campusfind_web_web::layouts>
