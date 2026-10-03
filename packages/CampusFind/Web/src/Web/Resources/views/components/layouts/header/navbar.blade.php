@php
    $navItems = collect(config('campusfind_web_web.navigation', []))
        ->sortBy('sort', SORT_NUMERIC)
        ->all();
@endphp

<nav class="hidden lg:flex items-center gap-2">
    @foreach ($navItems as $key => $item)
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
            class="px-4 py-2 text-base font-semibold rounded-xl transition-colors duration-150 text-decoration-none {{ $isActive ? 'text-blue-600 dark:text-blue-400 bg-blue-50/90 dark:bg-blue-950/60 font-bold' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100/80 dark:text-gray-200 dark:hover:text-white dark:hover:bg-gray-800/70' }}"
        >
            {{ trans($item['name'] ?? '') }}
        </a>
    @endforeach
</nav>
