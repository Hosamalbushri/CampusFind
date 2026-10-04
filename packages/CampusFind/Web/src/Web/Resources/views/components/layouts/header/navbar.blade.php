@php
    $allNavItems = collect(config('campusfind_web_web.navigation', []))
        ->sortBy('sort', SORT_NUMERIC)
        ->values();

    // Primary items in main bar (first 4 items: Home, Browse, Report Lost, Report Found)
    $primaryItems = $allNavItems->take(4);
    // Secondary items inside "More" dropdown
    $secondaryItems = $allNavItems->slice(4);

    $hasActiveSecondary = false;
    foreach ($secondaryItems as $item) {
        $routeName = $item['route'] ?? null;
        $params = $item['params'] ?? [];
        if ($routeName && request()->routeIs($routeName)) {
            if (empty($params) || (! empty($params['page']) && request()->route('page') === $params['page'])) {
                $hasActiveSecondary = true;
                break;
            }
        }
    }
@endphp

<nav class="hidden lg:flex items-center gap-1.5 xl:gap-2">
    @foreach ($primaryItems as $item)
        @php
            $routeName = $item['route'] ?? null;
            $params = $item['params'] ?? [];
            $url = $routeName && \Illuminate\Support\Facades\Route::has($routeName)
                ? route($routeName, $params)
                : ($item['url'] ?? '#');

            $isActive = false;
            if ($routeName && request()->routeIs($routeName)) {
                if (empty($params) || (! empty($params['page']) && request()->route('page') === $params['page'])) {
                    $isActive = true;
                }
            }
        @endphp

        <a
            href="{{ $url }}"
            class="whitespace-nowrap px-3.5 py-2 text-sm font-semibold rounded-xl transition-all duration-150 text-decoration-none {{ $isActive ? 'text-[#185c54] dark:text-emerald-300 bg-[#e6f4ee] dark:bg-emerald-950/70 border border-emerald-200/70 dark:border-emerald-800/60 shadow-2xs font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-[#185c54] dark:hover:text-emerald-400 hover:bg-slate-100/80 dark:hover:bg-slate-800/70' }}"
        >
            {{ trans($item['name'] ?? '') }}
        </a>
    @endforeach

    @if ($secondaryItems->isNotEmpty())
        <x-web::dropdown position="bottom-right">
            <x-slot:toggle>
                <button
                    type="button"
                    class="whitespace-nowrap inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold rounded-xl transition-all duration-150 border-0 bg-transparent cursor-pointer {{ $hasActiveSecondary ? 'text-[#185c54] dark:text-emerald-300 bg-[#e6f4ee] dark:bg-emerald-950/70 border border-emerald-200/70 dark:border-emerald-800/60 font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-[#185c54] dark:hover:text-emerald-400 hover:bg-slate-100/80 dark:hover:bg-slate-800/70' }}"
                    aria-label="@lang('campusfind_web_web::app.web.more')"
                >
                    <span>@lang('campusfind_web_web::app.web.more')</span>
                    <svg class="h-4 w-4 text-slate-400 transition-transform duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </x-slot:toggle>

            <x-slot:content>
                <div class="flex flex-col gap-1 p-1 min-w-[200px]">
                    @foreach ($secondaryItems as $item)
                        @php
                            $routeName = $item['route'] ?? null;
                            $params = $item['params'] ?? [];
                            $url = $routeName && \Illuminate\Support\Facades\Route::has($routeName)
                                ? route($routeName, $params)
                                : ($item['url'] ?? '#');

                            $isActive = false;
                            if ($routeName && request()->routeIs($routeName)) {
                                if (empty($params) || (! empty($params['page']) && request()->route('page') === $params['page'])) {
                                    $isActive = true;
                                }
                            }
                        @endphp

                        <a
                            href="{{ $url }}"
                            class="whitespace-nowrap flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold text-decoration-none transition-colors {{ $isActive ? 'bg-[#e6f4ee] dark:bg-emerald-950/60 text-[#185c54] dark:text-emerald-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                        >
                            <span>{{ trans($item['name'] ?? '') }}</span>
                            @if ($isActive)
                                <span class="h-1.5 w-1.5 rounded-full bg-[#185c54] dark:bg-emerald-400"></span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </x-slot:content>
        </x-web::dropdown>
    @endif
</nav>

