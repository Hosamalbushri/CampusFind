<header class="sticky top-0 z-50 w-full border-b border-gray-200/80 bg-white/85 backdrop-blur-md transition-colors dark:border-gray-800/80 dark:bg-gray-900/85">
    <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        
        <!-- Brand Logo & Identity -->
        <div class="flex items-center gap-8 xl:gap-12">
            <a href="{{ route('campusfind_web.web.home') }}" class="flex items-center gap-3 text-decoration-none group">
                @if (config('campusfind_web_web.branding.logo'))
                    <img src="{{ config('campusfind_web_web.branding.logo') }}" alt="{{ config('campusfind_web_web.branding.name', 'CampusFind') }}" class="h-10 w-auto">
                @else
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm group-hover:bg-blue-700 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                @endif

                <span class="text-xl sm:text-2xl font-bold tracking-tight text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                    {{ config('campusfind_web_web.branding.name') ? trans(config('campusfind_web_web.branding.name')) : trans('campusfind_web_web::app.web.title') }}
                </span>
            </a>

            <!-- Desktop Navigation Links -->
            <x-campusfind_web_web::layouts.header.navbar />
        </div>

        <!-- Utility Actions & Authentication -->
        <div class="flex items-center gap-2 sm:gap-3">
            @php
                $authConfig = config('campusfind_web_web.auth', []);
                $authEnabled = (bool) ($authConfig['enabled'] ?? false);
                $guard = $authConfig['guard'] ?? 'student';
                $isAuth = $authEnabled && $guard && auth()->guard($guard)->check();
                $user = $isAuth ? auth()->guard($guard)->user() : null;
            @endphp

            <!-- Language Switcher -->
            <a
                href="?locale={{ app()->getLocale() === 'ar' ? 'en' : 'ar' }}"
                class="inline-flex items-center px-3 py-2 rounded-xl text-sm font-semibold text-gray-700 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-200 dark:hover:text-white dark:hover:bg-gray-800 transition-colors text-decoration-none"
                title="{{ app()->getLocale() === 'ar' ? 'Switch to English' : 'التحويل إلى العربية' }}"
            >
                {{ app()->getLocale() === 'ar' ? 'EN' : 'عربي' }}
            </a>

            <!-- Dark Mode Toggle -->
            <button
                type="button"
                id="theme-toggle"
                data-action="toggle-dark-mode"
                class="inline-flex items-center justify-center h-10 w-10 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-800 cursor-pointer border-0 bg-transparent transition-colors focus:outline-none"
                aria-label="Toggle Dark Mode"
                title="Toggle Theme"
            >
                <svg class="h-5 w-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
                <svg class="h-5 w-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </button>

            <!-- Subtle Divider -->
            <div class="h-5 w-px bg-gray-200 dark:bg-gray-800 mx-1 hidden sm:block"></div>

            <!-- Student Authentication -->
            @if ($authEnabled)
                @if ($isAuth)
                    <!-- Authenticated Student Menu -->
                    <div class="hidden sm:flex items-center gap-2">
                        <a
                            href="{{ route('campusfind_web.web.account.dashboard') }}"
                            class="group inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-blue-50/80 hover:bg-blue-100/90 dark:bg-blue-950/50 dark:hover:bg-blue-900/60 border border-blue-200/70 dark:border-blue-800/60 transition-all text-decoration-none shadow-xs"
                            title="{{ $user->name ?? trans('campusfind_web_web::app.web.auth.account') }}"
                        >
                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-600 text-white shadow-xs group-hover:scale-105 transition-transform">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </span>
                            <span class="text-sm font-bold text-blue-700 dark:text-blue-300 group-hover:text-blue-800 dark:group-hover:text-blue-200 transition-colors">
                                @lang('campusfind_web_web::app.web.auth.account')
                            </span>
                        </a>

                        @if (! empty($authConfig['routes']['logout']) && \Illuminate\Support\Facades\Route::has($authConfig['routes']['logout']))
                            <form method="POST" action="{{ route($authConfig['routes']['logout']) }}" class="inline m-0">
                                @csrf
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center h-10 w-10 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40 border border-transparent hover:border-red-200/60 dark:hover:border-red-900/40 cursor-pointer bg-transparent transition-all focus:outline-none"
                                    title="@lang('campusfind_web_web::app.web.auth.logout')"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>
                @else
                    <!-- Guest Login CTA -->
                    @if (! empty($authConfig['routes']['login']) && \Illuminate\Support\Facades\Route::has($authConfig['routes']['login']))
                        <a
                            href="{{ route($authConfig['routes']['login']) }}"
                            class="hidden sm:inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm sm:text-base font-semibold text-white hover:bg-blue-700 transition-colors text-decoration-none shadow-xs"
                        >
                            @lang('campusfind_web_web::app.web.auth.login')
                        </a>
                    @endif
                @endif
            @endif

            <!-- Mobile Menu Trigger -->
            <button
                type="button"
                id="mobile-menu-button"
                data-action="toggle-mobile-menu"
                class="lg:hidden inline-flex items-center justify-center h-10 w-10 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-800 cursor-pointer border-0 bg-transparent transition-colors focus:outline-none"
                aria-controls="mobile-menu"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Drawer Menu -->
    <div
        id="mobile-menu"
        class="hidden lg:hidden border-b border-gray-200/80 bg-white/95 px-4 pt-3 pb-6 dark:border-gray-800/80 dark:bg-gray-900/95 backdrop-blur-md transition-all duration-150"
        aria-hidden="true"
    >
        @php
            $mobileNavItems = collect(config('campusfind_web_web.navigation', []))
                ->sortBy('sort', SORT_NUMERIC)
                ->all();
        @endphp

        <div class="flex flex-col space-y-1.5">
            @foreach ($mobileNavItems as $key => $item)
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
                    class="px-4 py-2.5 rounded-xl text-base font-semibold text-decoration-none transition-colors {{ $isActive ? 'bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800' }}"
                >
                    {{ trans($item['name'] ?? '') }}
                </a>
            @endforeach

            <!-- Auth Options in Mobile Drawer -->
            @if ($authEnabled)
                <div class="border-t border-gray-200 dark:border-gray-800 pt-3 mt-3">
                    @if ($isAuth)
                        <a href="{{ route('campusfind_web.web.account.dashboard') }}" class="group flex items-center justify-between px-4 py-3 rounded-xl bg-blue-50/70 dark:bg-blue-950/50 border border-blue-200/60 dark:border-blue-800/50 text-base font-bold text-blue-700 dark:text-blue-300 text-decoration-none transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-600 text-white shadow-xs">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </span>
                                <span>@lang('campusfind_web_web::app.web.auth.account')</span>
                            </div>
                        </a>

                        @if (! empty($authConfig['routes']['logout']) && \Illuminate\Support\Facades\Route::has($authConfig['routes']['logout']))
                            <form method="POST" action="{{ route($authConfig['routes']['logout']) }}" class="m-0 mt-2">
                                @csrf
                                <button type="submit" class="w-full text-left rtl:text-right px-4 py-2.5 rounded-xl text-base font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 bg-transparent border-0 cursor-pointer">
                                    @lang('campusfind_web_web::app.web.auth.logout')
                                </button>
                            </form>
                        @endif
                    @else
                        @if (! empty($authConfig['routes']['login']) && \Illuminate\Support\Facades\Route::has($authConfig['routes']['login']))
                            <a href="{{ route($authConfig['routes']['login']) }}" class="flex items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-base font-bold text-white hover:bg-blue-700 text-decoration-none text-center shadow-xs">
                                @lang('campusfind_web_web::app.web.auth.login')
                            </a>
                        @endif
                    @endif
                </div>
            @endif
        </div>
    </div>
</header>
