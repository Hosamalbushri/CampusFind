<x-web::layouts>
    <!-- Redesigned Hero Section Matching Refero Design -->
    <section class="relative isolate overflow-hidden pt-10 pb-16 sm:pt-14 sm:pb-20 lg:pt-16 lg:pb-24 bg-slate-50/40 dark:bg-slate-950 font-cairo">
        <x-web::container>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 xl:gap-20 items-center">
                
                <!-- Text Content Column (Right in RTL / order-2 lg:order-1) -->
                <div class="order-2 lg:order-1 lg:col-span-6 flex flex-col items-start text-start">
                    
                    <!-- Top Badge -->
                    <x-web::badge variant="mint" size="lg" class="mb-6 ring-4 ring-[#e6f4ee]/60 dark:ring-[#185c54]/10">
                        @lang('campusfind_web_web::app.web.hero.tag')
                    </x-web::badge>

                    <!-- Main Headline -->
                    <h1 class="text-4xl sm:text-5xl lg:text-[3.6rem] xl:text-[4rem] font-black tracking-tight text-slate-900 dark:text-white leading-[1.14] sm:leading-[1.12]">
                        <span>@lang('campusfind_web_web::app.web.hero.headline_main')</span>
                        <br>
                        <span class="inline-block mt-1 text-[#185c54] dark:text-[#a3e4c8]">@lang('campusfind_web_web::app.web.hero.headline_sub')</span>
                    </h1>

                    <!-- Subheadline / Description -->
                    <p class="mt-6 text-base sm:text-lg text-slate-600 dark:text-slate-300 leading-relaxed font-normal max-w-xl">
                        @lang('campusfind_web_web::app.web.hero.subheadline')
                    </p>

                    <!-- Action Buttons using Button Component -->
                    <div class="mt-8 sm:mt-10 flex flex-wrap items-center gap-3.5 sm:gap-4 w-full sm:w-auto">
                        <x-web::button
                            href="{{ route('campusfind_web.web.reports.lost') }}"
                            variant="primary"
                            size="xl"
                            class="w-full sm:w-auto"
                        >
                            @lang('campusfind_web_web::app.web.hero.primary_action')
                        </x-web::button>

                        <x-web::button
                            href="{{ route('campusfind_web.web.items.index') }}"
                            variant="secondary"
                            size="xl"
                            class="w-full sm:w-auto"
                        >
                            @lang('campusfind_web_web::app.web.hero.secondary_action')
                        </x-web::button>
                    </div>

                    <!-- Trust Indicators / Features -->
                    <div class="mt-8 sm:mt-10 flex flex-wrap items-center gap-y-2.5 gap-x-2.5 sm:gap-x-4 text-xs sm:text-sm text-slate-600 dark:text-slate-400 font-medium">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[#185c54] dark:text-emerald-400 font-bold">✓</span>
                            <span>@lang('campusfind_web_web::app.web.hero.trust_community')</span>
                        </div>
                        <span class="text-slate-300 dark:text-slate-700 hidden sm:inline">/</span>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[#185c54] dark:text-emerald-400 font-bold">✓</span>
                            <span>@lang('campusfind_web_web::app.web.hero.trust_verify')</span>
                        </div>
                        <span class="text-slate-300 dark:text-slate-700 hidden sm:inline">/</span>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[#185c54] dark:text-emerald-400 font-bold">✓</span>
                            <span>@lang('campusfind_web_web::app.web.hero.trust_staff')</span>
                        </div>
                    </div>

                </div>

                <!-- Visual Graphic Column (Left in RTL / order-1 lg:order-2) -->
                <div class="order-1 lg:order-2 lg:col-span-6 flex justify-center">
                    <div class="relative w-full max-w-[440px] sm:max-w-[480px] bg-[#e6f4ee] dark:bg-[#185c54]/20 rounded-[2.5rem] p-6 sm:p-10 flex items-center justify-center min-h-[380px] sm:min-h-[440px] shadow-sm border border-[#185c54]/10 dark:border-[#185c54]/20">
                        
                        <!-- Main Item Card (Tilted) -->
                        <div class="relative bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 shadow-2xl shadow-slate-300/50 dark:shadow-none border border-slate-100/80 dark:border-slate-800 w-full max-w-[270px] sm:max-w-[300px] transform -rotate-3 sm:-rotate-6 transition-all duration-300 hover:rotate-0 hover:scale-[1.02] z-0">
                            <!-- Image Box with Headphones -->
                            <div class="bg-[#789d91] dark:bg-[#5b7a70] rounded-2xl aspect-[4/3] flex items-center justify-center p-4 relative overflow-hidden shadow-inner">
                                <svg class="w-28 h-28 sm:w-32 sm:h-32 drop-shadow-md" viewBox="0 0 160 160" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <linearGradient id="hpHeadbandGrad" x1="20" y1="20" x2="140" y2="20" gradientUnits="userSpaceOnUse">
                                            <stop offset="0%" stop-color="#2d3742" />
                                            <stop offset="25%" stop-color="#475563" />
                                            <stop offset="50%" stop-color="#64748b" />
                                            <stop offset="75%" stop-color="#475563" />
                                            <stop offset="100%" stop-color="#2d3742" />
                                        </linearGradient>
                                        <linearGradient id="hpCushionGrad" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%" stop-color="#334155" />
                                            <stop offset="100%" stop-color="#1e293b" />
                                        </linearGradient>
                                        <linearGradient id="hpCupLeft" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" stop-color="#475569" />
                                            <stop offset="50%" stop-color="#334155" />
                                            <stop offset="100%" stop-color="#1e293b" />
                                        </linearGradient>
                                        <linearGradient id="hpCupRight" x1="100%" y1="0%" x2="0%" y2="100%">
                                            <stop offset="0%" stop-color="#475569" />
                                            <stop offset="50%" stop-color="#334155" />
                                            <stop offset="100%" stop-color="#1e293b" />
                                        </linearGradient>
                                        <linearGradient id="hpMetal" x1="0" y1="0" x2="1" y2="1">
                                            <stop offset="0%" stop-color="#94a3b8" />
                                            <stop offset="50%" stop-color="#e2e8f0" />
                                            <stop offset="100%" stop-color="#64748b" />
                                        </linearGradient>
                                    </defs>

                                    <!-- Headband Main Arc -->
                                    <path d="M 46 84 C 44 42, 60 26, 80 26 C 100 26, 116 42, 114 84" 
                                          stroke="url(#hpHeadbandGrad)" 
                                          stroke-width="12" 
                                          stroke-linecap="round" />
                                    
                                    <!-- Headband Inner Cushion -->
                                    <path d="M 54 70 C 55 42, 66 32, 80 32 C 94 32, 105 42, 106 70" 
                                          stroke="#1e293b" 
                                          stroke-width="5" 
                                          stroke-linecap="round" />

                                    <!-- Metal Extender Left -->
                                    <rect x="42" y="74" width="7" height="18" rx="3.5" fill="url(#hpMetal)" />
                                    <rect x="43.5" y="76" width="4" height="14" rx="2" fill="#334155" />

                                    <!-- Metal Extender Right -->
                                    <rect x="111" y="74" width="7" height="18" rx="3.5" fill="url(#hpMetal)" />
                                    <rect x="112.5" y="76" width="4" height="14" rx="2" fill="#334155" />

                                    <!-- Left Earcup -->
                                    <g transform="rotate(4, 45, 104)">
                                        <!-- Outer Shell -->
                                        <rect x="34" y="85" width="22" height="38" rx="11" fill="url(#hpCupLeft)" />
                                        <rect x="35" y="86" width="20" height="36" rx="10" stroke="#64748b" stroke-width="0.8" fill="none" opacity="0.6"/>
                                        <!-- Inner Cushion Pad -->
                                        <rect x="43" y="88" width="15" height="32" rx="7.5" fill="url(#hpCushionGrad)" />
                                        <rect x="46" y="92" width="9" height="24" rx="4.5" fill="#0f172a" />
                                    </g>

                                    <!-- Right Earcup -->
                                    <g transform="rotate(-4, 115, 104)">
                                        <!-- Outer Shell -->
                                        <rect x="104" y="85" width="22" height="38" rx="11" fill="url(#hpCupRight)" />
                                        <rect x="105" y="86" width="20" height="36" rx="10" stroke="#64748b" stroke-width="0.8" fill="none" opacity="0.6"/>
                                        <!-- Inner Cushion Pad -->
                                        <rect x="102" y="88" width="15" height="32" rx="7.5" fill="url(#hpCushionGrad)" />
                                        <rect x="105" y="92" width="9" height="24" rx="4.5" fill="#0f172a" />
                                    </g>
                                </svg>
                            </div>

                            <!-- Card Info -->
                            <div class="mt-4 px-1 pb-1">
                                <h3 class="font-bold text-slate-900 dark:text-white text-base sm:text-lg">
                                    @lang('campusfind_web_web::app.web.hero.sample_item.title')
                                </h3>
                                <p class="mt-1 text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                                    @lang('campusfind_web_web::app.web.hero.sample_item.location')
                                </p>
                            </div>
                        </div>

                        <!-- Floating Overlay Status Card -->
                        <div class="absolute -bottom-3 sm:-bottom-5 rtl:-left-2 rtl:sm:-left-6 ltr:-right-2 ltr:sm:-right-6 bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 shadow-2xl shadow-slate-400/30 dark:shadow-none border border-slate-200/80 dark:border-slate-800 w-[240px] sm:w-[275px] z-10 transition-transform duration-300 hover:scale-105">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">
                                    @lang('campusfind_web_web::app.web.hero.sample_item.today')
                                </span>
                                <x-web::badge variant="mint" size="xs" :dot="true">
                                    @lang('campusfind_web_web::app.web.hero.sample_item.match_badge')
                                </x-web::badge>
                            </div>
                            <h4 class="font-bold text-slate-900 dark:text-white text-sm sm:text-base mt-2.5">
                                @lang('campusfind_web_web::app.web.hero.sample_item.claim_prompt')
                            </h4>
                            <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                                @lang('campusfind_web_web::app.web.hero.sample_item.claim_hint')
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        </x-web::container>
    </section>

    <!-- Recent Found Items Section -->
    @if (! empty($recentItems))
        <x-web::section class="border-t border-slate-200/80 dark:border-slate-800/80">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-10">
                <div>
                    <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white sm:text-3xl">
                        @lang('campusfind_web_web::app.web.recent_found_items')
                    </h2>
                    <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">
                        @lang('campusfind_web_web::app.web.hero.subheadline')
                    </p>
                </div>

                <x-web::button href="{{ route('campusfind_web.web.items.index') }}" variant="ghost" size="sm" class="text-[#185c54] dark:text-[#a3e4c8]">
                    <span>@lang('campusfind_web_web::app.web.browse_items')</span>
                    <svg class="h-4 w-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </x-web::button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($recentItems as $item)
                    <x-web::campus.report-card :record="$item" />
                @endforeach
            </div>
        </x-web::section>
    @endif

    <!-- Highlights Section -->
    <x-web::section
        :badge="trans('campusfind_web_web::app.web.features')"
        :title="trans('campusfind_web_web::app.web.features')"
        :subtitle="trans('campusfind_web_web::app.web.hero.subheadline')"
        class="border-t border-slate-200/80 dark:border-slate-800/80"
    >
        <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
            <x-web::card variant="interactive" padding="lg">
                <div class="flex items-center gap-3.5 mb-4">
                    <div class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#e6f4ee] text-[#185c54] dark:bg-[#185c54]/30 dark:text-[#a3e4c8] ring-4 ring-[#e6f4ee]/60 dark:ring-[#185c54]/10 shadow-2xs">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        @lang('campusfind_web_web::app.web.highlights.fast.title')
                    </h3>
                </div>
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    @lang('campusfind_web_web::app.web.highlights.fast.description')
                </p>
            </x-web::card>

            <x-web::card variant="interactive" padding="lg">
                <div class="flex items-center gap-3.5 mb-4">
                    <div class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#e6f4ee] text-[#185c54] dark:bg-[#185c54]/30 dark:text-[#a3e4c8] ring-4 ring-[#e6f4ee]/60 dark:ring-[#185c54]/10 shadow-2xs">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        @lang('campusfind_web_web::app.web.highlights.modular.title')
                    </h3>
                </div>
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    @lang('campusfind_web_web::app.web.highlights.modular.description')
                </p>
            </x-web::card>

            <x-web::card variant="interactive" padding="lg">
                <div class="flex items-center gap-3.5 mb-4">
                    <div class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#e6f4ee] text-[#185c54] dark:bg-[#185c54]/30 dark:text-[#a3e4c8] ring-4 ring-[#e6f4ee]/60 dark:ring-[#185c54]/10 shadow-2xs">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        @lang('campusfind_web_web::app.web.highlights.bilingual.title')
                    </h3>
                </div>
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    @lang('campusfind_web_web::app.web.highlights.bilingual.description')
                </p>
            </x-web::card>
        </div>
    </x-web::section>

    <!-- FAQ Section Demonstrating Standardized x-web::accordion -->
    <x-web::section
        :badge="trans('campusfind_web_web::app.web.footer.link_faq')"
        :title="trans('campusfind_web_web::app.web.footer.link_faq')"
        :subtitle="trans('campusfind_web_web::app.web.how_it_works')"
        class="border-t border-slate-200/80 dark:border-slate-800/80"
    >
        <div class="w-full space-y-4">
            <x-web::accordion :isActive="true">
                <x-slot:header>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">
                        @lang('campusfind_web_web::app.web.how_it_works')
                    </h3>
                </x-slot:header>

                <x-slot:content>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        @lang('campusfind_web_web::app.web.hero.subheadline')
                    </p>
                </x-slot:content>
            </x-web::accordion>

            <x-web::accordion :isActive="false">
                <x-slot:header>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">
                        @lang('campusfind_web_web::app.web.browse_items')
                    </h3>
                </x-slot:header>

                <x-slot:content>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        @lang('campusfind_web_web::app.web.browse.subtitle')
                    </p>
                </x-slot:content>
            </x-web::accordion>

            <x-web::accordion :isActive="false">
                <x-slot:header>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">
                        @lang('campusfind_web_web::app.web.hero.claim_prompt')
                    </h3>
                </x-slot:header>

                <x-slot:content>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        @lang('campusfind_web_web::app.web.hero.claim_hint')
                    </p>
                </x-slot:content>
            </x-web::accordion>
        </div>
    </x-web::section>
</x-web::layouts>
