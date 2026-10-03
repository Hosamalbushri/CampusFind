<x-campusfind_web_web::layouts>
    <!-- Professional Hero Section -->
    <section class="relative isolate overflow-hidden pt-12 pb-20 sm:pt-16 sm:pb-24 bg-gradient-to-b from-blue-50/50 via-transparent to-transparent dark:from-gray-900/60 dark:via-gray-950 dark:to-gray-950">
        <!-- Background Ambient Glow -->
        <div class="pointer-events-none absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
            <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-blue-600/20 to-indigo-600/20 opacity-40 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]"></div>
        </div>

        <x-campusfind_web_web::container class="text-center">
            <!-- Top Announcement Pill -->
            <div class="inline-flex items-center gap-2.5 rounded-full border border-blue-200/80 bg-blue-50/90 px-4 py-1.5 text-xs sm:text-sm font-semibold text-blue-700 dark:border-blue-900/60 dark:bg-blue-950/60 dark:text-blue-300 shadow-xs mb-6 backdrop-blur-xs">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
                </span>
                <span>@lang('campusfind_web_web::app.web.title')</span>
                <span class="text-blue-300 dark:text-blue-700">•</span>
                <span>@lang('campusfind_web_web::app.web.browse_items')</span>
            </div>

            <!-- Main Headline -->
            <h1 class="text-4xl font-black tracking-tight text-gray-900 dark:text-white sm:text-5xl lg:text-6xl max-w-4xl mx-auto leading-[1.18]">
                @lang('campusfind_web_web::app.web.hero.headline')
            </h1>

            <!-- Subtitle -->
            <p class="mx-auto mt-6 max-w-2xl text-base sm:text-lg text-gray-600 dark:text-gray-300 leading-relaxed font-normal">
                @lang('campusfind_web_web::app.web.hero.subheadline')
            </p>

            <!-- Search Bar Component -->
            <div class="mt-10 max-w-3xl mx-auto">
                <form action="{{ route('campusfind_web.web.items.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-2 p-2 bg-white dark:bg-gray-800 rounded-2xl shadow-xl shadow-blue-500/5 border border-gray-200/80 dark:border-gray-700/80 transition-shadow hover:shadow-2xl">
                    <div class="flex-1 flex items-center px-3.5 w-full">
                        <svg class="h-5 w-5 text-gray-400 ltr:mr-3 rtl:ml-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input
                            type="text"
                            name="query"
                            placeholder="@lang('campusfind_web_web::app.web.search_placeholder')"
                            class="w-full bg-transparent border-0 text-sm sm:text-base text-gray-800 dark:text-gray-100 placeholder-gray-400 focus:outline-none py-3"
                        />
                    </div>

                    @if (! empty($categories))
                        <div class="w-full sm:w-52 border-t sm:border-t-0 sm:border-l dark:border-gray-700 rtl:sm:border-l-0 rtl:sm:border-r px-3 py-1 flex items-center">
                            <select name="category" class="w-full bg-transparent border-0 text-sm font-medium text-gray-700 dark:text-gray-300 focus:outline-none py-2.5 cursor-pointer">
                                <option value="">@lang('campusfind_web_web::app.web.all_categories')</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->code }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <button
                        type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-6 py-3.5 text-sm sm:text-base font-bold text-white shadow-xs transition-colors cursor-pointer border-0 shrink-0"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span>@lang('campusfind_web_web::app.web.search')</span>
                    </button>
                </form>
            </div>

            <!-- Quick Action Links -->
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3 sm:gap-4">
                <a
                    href="{{ route('campusfind_web.web.items.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-5 py-3 text-sm sm:text-base font-bold text-white shadow-xs transition-colors text-decoration-none"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span>@lang('campusfind_web_web::app.web.hero.primary_action')</span>
                </a>

                <a
                    href="{{ route('campusfind_web.web.pages.show', ['page' => 'how-it-works']) }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 px-5 py-3 text-sm sm:text-base font-semibold text-gray-800 dark:text-gray-200 transition-colors text-decoration-none"
                >
                    <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>@lang('campusfind_web_web::app.web.how_it_works')</span>
                </a>
            </div>
        </x-campusfind_web_web::container>
    </section>

    <!-- Recent Found Items Section -->
    @if (! empty($recentItems))
        <x-campusfind_web_web::section class="border-t border-gray-100 dark:border-gray-800/80">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl">
                        @lang('campusfind_web_web::app.web.recent_found_items')
                    </h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        @lang('campusfind_web_web::app.web.hero.subheadline')
                    </p>
                </div>

                <a href="{{ route('campusfind_web.web.items.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 inline-flex items-center gap-1 text-decoration-none">
                    <span>@lang('campusfind_web_web::app.web.browse_items')</span>
                    <svg class="h-4 w-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($recentItems as $item)
                    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden shadow-xs hover:shadow-md transition-shadow flex flex-col">
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
                                    class="text-sm font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 inline-flex items-center gap-1 text-decoration-none"
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
        </x-campusfind_web_web::section>
    @endif

    <!-- Highlights Section -->
    <x-campusfind_web_web::section class="border-t border-gray-100 dark:border-gray-800/80">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                @lang('campusfind_web_web::app.web.features')
            </h2>
        </div>

        <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
            <x-campusfind_web_web::card>
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-950/80 dark:text-blue-400 mb-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    @lang('campusfind_web_web::app.web.highlights.fast.title')
                </h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                    @lang('campusfind_web_web::app.web.highlights.fast.description')
                </p>
            </x-campusfind_web_web::card>

            <x-campusfind_web_web::card>
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-950/80 dark:text-blue-400 mb-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    @lang('campusfind_web_web::app.web.highlights.modular.title')
                </h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                    @lang('campusfind_web_web::app.web.highlights.modular.description')
                </p>
            </x-campusfind_web_web::card>

            <x-campusfind_web_web::card>
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-950/80 dark:text-blue-400 mb-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    @lang('campusfind_web_web::app.web.highlights.bilingual.title')
                </h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                    @lang('campusfind_web_web::app.web.highlights.bilingual.description')
                </p>
            </x-campusfind_web_web::card>
        </div>
    </x-campusfind_web_web::section>
</x-campusfind_web_web::layouts>
