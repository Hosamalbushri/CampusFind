<header class="sticky top-0 z-50 w-full border-b border-slate-200/80 bg-white/90 backdrop-blur-md transition-colors dark:border-slate-800/80 dark:bg-slate-950/90 font-cairo">
    <div class="mx-auto flex h-16 sm:h-18 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        
        <!-- Brand Logo & Main Navigation -->
        <div class="flex items-center gap-6 xl:gap-8">
            <a href="{{ route('campusfind_web.web.home') }}" class="flex items-center gap-2.5 text-decoration-none group select-none">
                @if (config('campusfind_web_web.branding.logo'))
                    <img src="{{ config('campusfind_web_web.branding.logo') }}" alt="{{ config('campusfind_web_web.branding.name', 'CampusFind') }}" class="h-9 w-auto">
                @else
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#185c54] text-white shadow-xs group-hover:bg-[#134942] transition-colors">
                        <svg class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                @endif

                <div class="flex flex-col">
                    <span class="text-xl font-black tracking-tight text-slate-900 dark:text-white leading-none">
                        Campus<span class="text-[#185c54] dark:text-emerald-400">Find</span>
                    </span>
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <x-web::layouts.header.navbar />
        </div>

        <!-- Utility Actions & Authentication -->
        <div class="flex items-center gap-2 sm:gap-2.5">
            @php
                $authConfig = config('campusfind_web_web.auth', []);
                $authEnabled = (bool) ($authConfig['enabled'] ?? false);
                $guard = $authConfig['guard'] ?? 'student';
                $isAuth = $authEnabled && $guard && auth()->guard($guard)->check();
                $locales = [
                    'ar' => ['name' => 'العربية', 'native' => 'العربية'],
                    'en' => ['name' => 'English', 'native' => 'English'],
                ];
                $currentLocale = app()->getLocale();
                $currentLocaleData = $locales[$currentLocale] ?? $locales['ar'];
            @endphp

            <!-- Quick Search Button -->
            <a
                href="{{ route('campusfind_web.web.items.index') }}"
                class="hidden md:inline-flex items-center justify-center h-9 w-9 rounded-xl text-slate-500 hover:text-[#185c54] hover:bg-slate-100/80 dark:text-slate-400 dark:hover:text-emerald-400 dark:hover:bg-slate-800/80 transition-colors border border-slate-200/70 dark:border-slate-700/70 text-decoration-none"
                title="@lang('campusfind_web_web::app.web.search')"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </a>

            <!-- Language Switcher Dropdown -->
            <div class="inline-block">
                <x-web::dropdown position="bottom-{{ in_array(app()->getLocale(), ['fa', 'ar']) ? 'left' : 'right' }}">
                    <x-slot:toggle>
                        <button
                            type="button"
                            class="whitespace-nowrap inline-flex items-center gap-1.5 h-9 px-2.5 sm:px-3 rounded-xl text-xs font-bold text-slate-700 hover:text-slate-900 bg-slate-100/90 hover:bg-slate-200/80 dark:bg-slate-800/90 dark:text-slate-200 dark:hover:bg-slate-700/90 transition-all border border-slate-200/70 dark:border-slate-700/70 focus:outline-none select-none group"
                        >
                            <svg class="h-3.5 w-3.5 text-slate-500 dark:text-slate-400 group-hover:text-slate-700 dark:group-hover:text-slate-200 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                            </svg>
                            <span class="hidden sm:inline">{{ $currentLocaleData['native'] }}</span>
                            <span class="sm:hidden font-extrabold uppercase text-[11px]">{{ app()->getLocale() === 'ar' ? 'AR' : 'EN' }}</span>
                            <svg class="h-3.5 w-3.5 text-slate-400 group-hover:text-slate-600 dark:text-slate-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot:content class="w-40 p-1.5 shadow-xl ring-1 ring-slate-900/5 rounded-2xl">
                        <div class="flex flex-col space-y-0.5">
                            @foreach ($locales as $code => $data)
                                <a
                                    href="?locale={{ $code }}"
                                    class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold text-decoration-none transition-colors {{ $currentLocale === $code ? 'bg-[#e6f4ee] dark:bg-emerald-950/60 text-[#185c54] dark:text-emerald-300' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-200 dark:hover:text-white dark:hover:bg-slate-800' }}"
                                >
                                    <span>{{ $data['native'] }}</span>
                                    @if ($currentLocale === $code)
                                        <svg class="h-3.5 w-3.5 text-[#185c54] dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </x-slot>
                </x-web::dropdown>
            </div>

            <!-- Vue-driven Dark Mode Toggle -->
            <v-dark>
                <button
                    type="button"
                    class="inline-flex items-center justify-center h-9 w-9 rounded-xl text-slate-500 hover:text-slate-900 hover:bg-slate-100/80 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800/80 border border-slate-200/70 dark:border-slate-700/70 cursor-pointer bg-transparent transition-all focus:outline-none"
                    aria-label="{{ trans('campusfind_web_web::app.web.accessibility.toggle_theme') }}"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
            </v-dark>

            <!-- Subtle Divider -->
            <div class="h-5 w-px bg-slate-200 dark:bg-slate-800 mx-0.5 hidden sm:block"></div>

            <!-- Student Authentication -->
            @if ($authEnabled)
                @if ($isAuth)
                    <!-- Authenticated User Profile Dropdown -->
                    <div class="hidden sm:block">
                        <x-web::dropdown position="bottom-{{ in_array(app()->getLocale(), ['fa', 'ar']) ? 'left' : 'right' }}">
                            <x-slot:toggle>
                                <button
                                    type="button"
                                    class="whitespace-nowrap inline-flex items-center gap-2 h-9 px-3 rounded-xl bg-slate-100/90 hover:bg-slate-200/80 dark:bg-slate-800/90 dark:hover:bg-slate-700/90 border border-slate-200/70 dark:border-slate-700/70 transition-all cursor-pointer shadow-2xs focus:outline-none group select-none"
                                >
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-[#185c54] text-white text-xs font-black uppercase shadow-xs">
                                        {{ mb_strtoupper(mb_substr($user->name ?? 'U', 0, 1)) }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200 group-hover:text-slate-900 dark:group-hover:text-white max-w-[120px] truncate transition-colors">
                                        {{ $user->name ?? trans('campusfind_web_web::app.web.auth.account') }}
                                    </span>
                                    <svg class="h-3.5 w-3.5 text-slate-400 group-hover:text-slate-600 dark:text-slate-400 dark:group-hover:text-slate-200 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot:content class="w-60 p-2 shadow-xl ring-1 ring-slate-900/5 rounded-2xl">
                                <div class="px-3.5 py-2.5 border-b border-slate-100 dark:border-slate-800/80 mb-1 flex items-center gap-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#185c54] text-white text-sm font-bold shadow-xs">
                                        {{ mb_substr($user->name ?? 'U', 0, 1) }}
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-bold text-slate-900 dark:text-white truncate leading-tight">{{ $user->name ?? '' }}</p>
                                        <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 truncate mt-0.5">@lang('campusfind_web_web::app.web.auth.account')</p>
                                    </div>
                                </div>

                                <a
                                    href="{{ route('campusfind_web.web.account.dashboard') }}"
                                    class="flex items-center gap-2.5 px-3 py-2 text-sm font-semibold rounded-xl text-slate-700 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-200 dark:hover:text-white dark:hover:bg-slate-800 text-decoration-none transition-colors"
                                >
                                    <svg class="h-4 w-4 text-[#185c54] dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    <span>@lang('campusfind_web_web::app.web.auth.dashboard')</span>
                                </a>

                                @if (! empty($authConfig['routes']['logout']) && \Illuminate\Support\Facades\Route::has($authConfig['routes']['logout']))
                                    <x-web::form
                                        method="POST"
                                        :action="route($authConfig['routes']['logout'])"
                                        id="headerLogoutForm"
                                        class="hidden"
                                    >
                                    </x-web::form>

                                    <button
                                        type="button"
                                        onclick="window.app.config.globalProperties.$emitter.emit('open-confirm-modal', {
                                            title: '{{ trans('campusfind_web_web::app.web.auth.logout_confirm_title') }}',
                                            message: '{{ trans('campusfind_web_web::app.web.auth.logout_confirm_desc') }}',
                                            options: {
                                                btnDisagree: '{{ trans('campusfind_web_web::app.web.cancel') }}',
                                                btnAgree: '{{ trans('campusfind_web_web::app.web.auth.logout') }}'
                                            },
                                            agree: () => { document.getElementById('headerLogoutForm').submit(); }
                                        })"
                                        class="w-full flex items-center gap-2 px-3 py-2 text-sm font-semibold rounded-lg text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40 bg-transparent border-0 cursor-pointer text-left rtl:text-right transition-colors"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        <span>@lang('campusfind_web_web::app.web.auth.logout')</span>
                                    </button>
                                @endif
                            </x-slot>
                        </x-web::dropdown>
                    </div>
                @else
                    <!-- Guest Login CTA -->
                    @if (! empty($authConfig['routes']['login']) && \Illuminate\Support\Facades\Route::has($authConfig['routes']['login']))
                        <x-web::button
                            href="{{ route($authConfig['routes']['login']) }}"
                            variant="primary"
                            size="sm"
                            class="whitespace-nowrap hidden sm:inline-flex items-center"
                        >
                            <span>@lang('campusfind_web_web::app.web.auth.login')</span>
                        </x-web::button>
                    @endif
                @endif
            @endif

            <!-- Mobile Menu Trigger -->
            <button
                type="button"
                id="mobile-menu-button"
                data-action="toggle-mobile-menu"
                class="lg:hidden inline-flex items-center justify-center h-9 w-9 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-800 border border-slate-200/70 dark:border-slate-700/70 cursor-pointer bg-transparent transition-colors focus:outline-none"
                aria-controls="mobile-menu"
                aria-expanded="false"
                aria-label="{{ trans('campusfind_web_web::app.web.accessibility.toggle_navigation') }}"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Drawer Menu -->
    <div
        id="mobile-menu"
        class="hidden lg:hidden border-b border-slate-200/80 bg-white/95 px-4 pt-3 pb-6 dark:border-slate-800/80 dark:bg-slate-950/95 backdrop-blur-md transition-all duration-150 font-cairo"
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
                    class="whitespace-nowrap px-4 py-2.5 rounded-xl text-sm font-semibold text-decoration-none transition-colors {{ $isActive ? 'bg-[#e6f4ee] dark:bg-emerald-950/60 text-[#185c54] dark:text-emerald-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                >
                    {{ trans($item['name'] ?? '') }}
                </a>
            @endforeach

            <!-- Auth Options in Mobile Drawer -->
            @if ($authEnabled)
                <div class="border-t border-slate-200 dark:border-slate-800 pt-3 mt-3">
                    @if ($isAuth)
                        <a href="{{ route('campusfind_web.web.account.dashboard') }}" class="group flex items-center justify-between px-4 py-3 rounded-2xl bg-slate-100/90 dark:bg-slate-800/90 border border-slate-200/70 dark:border-slate-700/70 text-sm font-bold text-slate-800 dark:text-slate-200 text-decoration-none transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-[#185c54] text-white shadow-xs text-xs font-bold">
                                    {{ mb_substr($user->name ?? 'U', 0, 1) }}
                                </span>
                                <span>{{ $user->name ?? trans('campusfind_web_web::app.web.auth.account') }}</span>
                            </div>
                        </a>

                        @if (! empty($authConfig['routes']['logout']) && \Illuminate\Support\Facades\Route::has($authConfig['routes']['logout']))
                            <button
                                type="button"
                                onclick="window.app.config.globalProperties.$emitter.emit('open-confirm-modal', {
                                    title: '{{ trans('campusfind_web_web::app.web.auth.logout_confirm_title') }}',
                                    message: '{{ trans('campusfind_web_web::app.web.auth.logout_confirm_desc') }}',
                                    options: {
                                        btnDisagree: '{{ trans('campusfind_web_web::app.web.cancel') }}',
                                        btnAgree: '{{ trans('campusfind_web_web::app.web.auth.logout') }}'
                                    },
                                    agree: () => { document.getElementById('headerLogoutForm').submit(); }
                                })"
                                class="w-full text-left rtl:text-right px-4 py-2.5 rounded-xl text-sm font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 bg-transparent border-0 cursor-pointer"
                            >
                                @lang('campusfind_web_web::app.web.auth.logout')
                            </button>
                        @endif
                    @else
                        @if (! empty($authConfig['routes']['login']) && \Illuminate\Support\Facades\Route::has($authConfig['routes']['login']))
                            <a href="{{ route($authConfig['routes']['login']) }}" class="flex items-center justify-center rounded-xl bg-[#185c54] px-5 py-2.5 text-sm font-bold text-white hover:bg-[#134942] text-decoration-none text-center shadow-xs">
                                <span>@lang('campusfind_web_web::app.web.auth.login')</span>
                            </a>
                        @endif
                    @endif
                </div>
            @endif
            <!-- Language Switcher in Mobile Drawer -->
            <div class="border-t border-slate-200 dark:border-slate-800 pt-3.5 mt-3 space-y-2">
                <div class="px-1 text-xs font-bold text-slate-500 dark:text-slate-400 flex items-center gap-2">
                    <svg class="h-4 w-4 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                    </svg>
                    <span>اللغة / Language</span>
                </div>
                <div class="grid grid-cols-2 gap-1.5 p-1 bg-slate-100 dark:bg-slate-900 rounded-2xl border border-slate-200/70 dark:border-slate-800 select-none">
                    @foreach ($locales as $code => $data)
                        <a
                            href="?locale={{ $code }}"
                            class="flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-xs font-bold text-decoration-none transition-all {{ $currentLocale === $code ? 'bg-white dark:bg-slate-800 text-[#185c54] dark:text-emerald-400 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}"
                        >
                            <span>{{ $data['native'] }}</span>
                            @if ($currentLocale === $code)
                                <svg class="h-3.5 w-3.5 text-[#185c54] dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</header>

@pushOnce('scripts')
    <script type="text/x-template" id="v-dark-template">
        <button
            type="button"
            class="inline-flex items-center justify-center h-9 w-9 rounded-xl text-slate-500 hover:text-slate-900 hover:bg-slate-100/80 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800/80 border border-slate-200/70 dark:border-slate-700/70 cursor-pointer bg-transparent transition-all focus:outline-none"
            :title="isDarkMode ? 'التبديل إلى الوضع النهاري' : 'التبديل إلى الوضع الليلي'"
            aria-label="Toggle dark mode"
            @click="toggle"
        >
            <svg v-if="!isDarkMode" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
            <svg v-else class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </button>
    </script>

    <script type="module">
        app.component('v-dark', {
            template: '#v-dark-template',

            data() {
                return {
                    isDarkMode: {{ request()->cookie('dark_mode') ? 1 : 0 }},
                };
            },

            methods: {
                toggle() {
                    this.isDarkMode = parseInt(this.isDarkModeCookie()) ? 0 : 1;

                    var expiryDate = new Date();
                    expiryDate.setMonth(expiryDate.getMonth() + 1);

                    document.cookie = 'dark_mode=' + this.isDarkMode + '; path=/; expires=' + expiryDate.toGMTString();
                    document.documentElement.classList.toggle('dark', this.isDarkMode === 1);

                    if (this.$emitter) {
                        this.$emitter.emit('change-theme', this.isDarkMode ? 'dark' : 'light');
                    }
                },

                isDarkModeCookie() {
                    const cookies = document.cookie.split(';');

                    for (const cookie of cookies) {
                        const [name, value] = cookie.trim().split('=');

                        if (name === 'dark_mode') {
                            return value;
                        }
                    }

                    return {{ request()->cookie('dark_mode') ? 1 : 0 }};
                },
            },
        });
    </script>
@endPushOnce
