<footer class="mt-auto border-t border-slate-200/80 bg-white pt-14 pb-10 transition-colors dark:border-slate-800/80 dark:bg-slate-950 font-cairo">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <!-- Main Footer Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 pb-12">
            
            <!-- Left Column: Brand Identity & Newsletter -->
            <div class="lg:col-span-6 xl:col-span-5 space-y-6">
                <!-- Brand Logo & Name -->
                <a href="{{ route('campusfind_web.web.home') }}" class="inline-flex items-center gap-3 text-decoration-none group">
                    @if (config('campusfind_web_web.branding.logo'))
                        <img src="{{ config('campusfind_web_web.branding.logo') }}" alt="{{ config('campusfind_web_web.branding.name', 'CampusFind') }}" class="h-9 w-auto">
                    @else
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#185c54] text-white shadow-xs group-hover:bg-[#134942] transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    @endif
                    <span class="text-xl font-black tracking-tight text-slate-900 dark:text-white group-hover:text-[#185c54] dark:group-hover:text-emerald-400 transition-colors">
                        {{ config('campusfind_web_web.branding.name') ? trans(config('campusfind_web_web.branding.name')) : trans('campusfind_web_web::app.web.title') }}
                    </span>
                </a>

                <!-- Headline & Subtitle -->
                <div class="space-y-2">
                    <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-snug">
                        @lang('campusfind_web_web::app.web.footer.newsletter_title')
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 max-w-md leading-relaxed">
                        @lang('campusfind_web_web::app.web.footer.newsletter_subtitle')
                    </p>
                </div>

                <!-- Newsletter Input Form -->
                <div class="space-y-2.5 max-w-md">
                    <form
                        class="flex flex-col sm:flex-row gap-2.5"
                        onsubmit="event.preventDefault(); const input = this.querySelector('input'); if (input && input.value) { const msg = this.nextElementSibling; if (msg) { msg.classList.remove('hidden'); } input.value = ''; }"
                    >
                        <div class="relative flex-1">
                            <input
                                type="email"
                                required
                                placeholder="@lang('campusfind_web_web::app.web.footer.email_placeholder')"
                                class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900/90 px-4 py-3 text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#185c54] focus:ring-4 focus:ring-[#185c54]/15 transition-all"
                            />
                        </div>

                        <x-web::button
                            type="submit"
                            variant="primary"
                            size="md"
                            class="flex-shrink-0"
                        >
                            @lang('campusfind_web_web::app.web.footer.subscribe')
                        </x-web::button>
                    </form>

                    <!-- Client-side confirmation hint (hidden by default) -->
                    <p class="hidden text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                        @lang('campusfind_web_web::app.web.footer.privacy_notice')
                    </p>

                    <!-- Privacy disclaimer -->
                    <p class="text-xs text-slate-500 dark:text-slate-500 leading-normal">
                        @lang('campusfind_web_web::app.web.footer.privacy_notice')
                    </p>
                </div>
            </div>

            <!-- Right Columns: Navigation Links (3 Columns) -->
            <div class="lg:col-span-6 xl:col-span-7 grid grid-cols-2 sm:grid-cols-3 gap-8">
                <!-- Column 1: Explore -->
                <div class="space-y-4">
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                        @lang('campusfind_web_web::app.web.footer.col_explore')
                    </h4>
                    <ul class="space-y-2.5 text-sm list-none p-0 m-0">
                        <li>
                            <a href="{{ route('campusfind_web.web.items.index') }}" class="text-slate-600 dark:text-slate-400 hover:text-[#185c54] dark:hover:text-[#a3e4c8] text-decoration-none transition-colors">
                                @lang('campusfind_web_web::app.web.footer.link_browse')
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('campusfind_web.web.home') }}#recent" class="text-slate-600 dark:text-slate-400 hover:text-[#185c54] dark:hover:text-[#a3e4c8] text-decoration-none transition-colors">
                                @lang('campusfind_web_web::app.web.footer.link_recent')
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('campusfind_web.web.items.index') }}" class="text-slate-600 dark:text-slate-400 hover:text-[#185c54] dark:hover:text-[#a3e4c8] text-decoration-none transition-colors">
                                @lang('campusfind_web_web::app.web.all_categories')
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Column 2: Services -->
                <div class="space-y-4">
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                        @lang('campusfind_web_web::app.web.footer.col_services')
                    </h4>
                    <ul class="space-y-2.5 text-sm list-none p-0 m-0">
                        <li>
                            <a href="{{ route('campusfind_web.web.pages.show', ['page' => 'how-it-works']) }}" class="text-slate-600 dark:text-slate-400 hover:text-[#185c54] dark:hover:text-[#a3e4c8] text-decoration-none transition-colors">
                                @lang('campusfind_web_web::app.web.footer.link_how_it_works')
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('campusfind_web.web.login') }}" class="text-slate-600 dark:text-slate-400 hover:text-[#185c54] dark:hover:text-[#a3e4c8] text-decoration-none transition-colors">
                                @lang('campusfind_web_web::app.web.footer.link_report')
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('campusfind_web.web.account.dashboard') }}" class="text-slate-600 dark:text-slate-400 hover:text-[#185c54] dark:hover:text-[#a3e4c8] text-decoration-none transition-colors">
                                @lang('campusfind_web_web::app.web.footer.link_portal')
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Column 3: About & Support -->
                <div class="space-y-4 col-span-2 sm:col-span-1">
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                        @lang('campusfind_web_web::app.web.footer.col_about')
                    </h4>
                    <ul class="space-y-2.5 text-sm list-none p-0 m-0">
                        <li>
                            <a href="{{ route('campusfind_web.web.pages.show', ['page' => 'about']) }}" class="text-slate-600 dark:text-slate-400 hover:text-[#185c54] dark:hover:text-[#a3e4c8] text-decoration-none transition-colors">
                                @lang('campusfind_web_web::app.web.footer.link_about')
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('campusfind_web.web.pages.show', ['page' => 'contact']) }}" class="text-slate-600 dark:text-slate-400 hover:text-[#185c54] dark:hover:text-[#a3e4c8] text-decoration-none transition-colors">
                                @lang('campusfind_web_web::app.web.footer.link_contact')
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('campusfind_web.web.pages.show', ['page' => 'faq']) }}" class="text-slate-600 dark:text-slate-400 hover:text-[#185c54] dark:hover:text-[#a3e4c8] text-decoration-none transition-colors">
                                @lang('campusfind_web_web::app.web.footer.link_faq')
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Bottom Bar: Copyright & Social Links -->
        <div class="border-t border-slate-200/80 dark:border-slate-800/80 pt-8 flex flex-col-reverse sm:flex-row items-center justify-between gap-4">
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium text-center sm:text-start m-0">
                &copy; {{ date('Y') }} {{ config('campusfind_web_web.branding.name') ? trans(config('campusfind_web_web.branding.name')) : 'CampusFind' }}. @lang('campusfind_web_web::app.web.footer.rights')
            </p>

            <!-- Social Links / Channels -->
            <div class="flex items-center gap-2">
                <!-- GitHub -->
                <a
                    href="https://github.com"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="h-9 w-9 rounded-xl bg-slate-100 hover:bg-[#e6f4ee] dark:bg-slate-800 dark:hover:bg-[#185c54]/30 text-slate-600 hover:text-[#185c54] dark:text-slate-400 dark:hover:text-[#a3e4c8] flex items-center justify-center transition-colors shadow-xs text-decoration-none"
                    aria-label="GitHub"
                >
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                    </svg>
                </a>

                <!-- X (Twitter) -->
                <a
                    href="https://x.com"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="h-9 w-9 rounded-xl bg-slate-100 hover:bg-[#e6f4ee] dark:bg-slate-800 dark:hover:bg-[#185c54]/30 text-slate-600 hover:text-[#185c54] dark:text-slate-400 dark:hover:text-[#a3e4c8] flex items-center justify-center transition-colors shadow-xs text-decoration-none"
                    aria-label="X"
                >
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                </a>

                <!-- LinkedIn -->
                <a
                    href="https://linkedin.com"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="h-9 w-9 rounded-xl bg-slate-100 hover:bg-[#e6f4ee] dark:bg-slate-800 dark:hover:bg-[#185c54]/30 text-slate-600 hover:text-[#185c54] dark:text-slate-400 dark:hover:text-[#a3e4c8] flex items-center justify-center transition-colors shadow-xs text-decoration-none"
                    aria-label="LinkedIn"
                >
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                    </svg>
                </a>

                <!-- YouTube -->
                <a
                    href="https://youtube.com"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="h-9 w-9 rounded-xl bg-slate-100 hover:bg-[#e6f4ee] dark:bg-slate-800 dark:hover:bg-[#185c54]/30 text-slate-600 hover:text-[#185c54] dark:text-slate-400 dark:hover:text-[#a3e4c8] flex items-center justify-center transition-colors shadow-xs text-decoration-none"
                    aria-label="YouTube"
                >
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</footer>
