<x-campusfind_web_web::layouts>
    <x-campusfind_web_web::container class="max-w-5xl py-10">
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-6">
            <a href="{{ route('campusfind_web.web.home') }}" class="hover:text-[var(--brand-color)] text-decoration-none">
                @lang('campusfind_web_web::app.web.home')
            </a>
            <span>/</span>
            <span class="font-medium text-gray-800 dark:text-gray-200">
                @lang('campusfind_web_web::app.web.auth.dashboard')
            </span>
        </nav>

        <!-- Student Profile Card -->
        <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-200 dark:border-gray-800 p-8 shadow-sm mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 dark:border-gray-800 pb-6">
                <div class="flex items-center gap-4">
                    <div class="h-16 w-16 rounded-2xl bg-blue-100 text-[var(--brand-color)] dark:bg-blue-950 dark:text-blue-400 flex items-center justify-center font-bold text-2xl shadow-sm">
                        {{ mb_substr($user->name ?? 'S', 0, 1) }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">
                            {{ $user->name ?? 'Student User' }}
                        </h1>
                        <p class="text-sm font-mono text-gray-500 dark:text-gray-400 mt-0.5">
                            @lang('campusfind_web_web::app.web.auth.card_number'): {{ $user->university_card_number ?? '-' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800 dark:bg-green-900/30 dark:text-green-300">
                        @lang('campusfind_web_web::app.web.auth.active_student')
                    </span>

                    <form method="POST" action="{{ route('campusfind_web.web.logout') }}" class="m-0">
                        @csrf
                        <button
                            type="submit"
                            class="rounded-xl border border-red-200 dark:border-red-900/50 bg-red-50 dark:bg-red-950/40 px-4 py-2 text-xs font-bold text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/60 cursor-pointer transition-colors"
                        >
                            @lang('campusfind_web_web::app.web.auth.logout')
                        </button>
                    </form>
                </div>
            </div>

            <!-- Student Academic Details -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-6 text-sm">
                <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-2xl border border-gray-100 dark:border-gray-800">
                    <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 block mb-1">@lang('campusfind_web_web::app.web.auth.registration_number')</span>
                    <span class="font-bold text-gray-800 dark:text-gray-200">{{ $user->registration_number ?? '-' }}</span>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-2xl border border-gray-100 dark:border-gray-800">
                    <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 block mb-1">@lang('campusfind_web_web::app.web.auth.major')</span>
                    <span class="font-bold text-gray-800 dark:text-gray-200">{{ $user->major ?? '-' }}</span>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-2xl border border-gray-100 dark:border-gray-800">
                    <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 block mb-1">@lang('campusfind_web_web::app.web.auth.academic_level')</span>
                    <span class="font-bold text-gray-800 dark:text-gray-200">{{ $user->academic_level ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Services Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-200 dark:border-gray-800 p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="h-10 w-10 rounded-xl bg-blue-100 text-[var(--brand-color)] dark:bg-blue-950 dark:text-blue-400 flex items-center justify-center mb-3">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        @lang('campusfind_web_web::app.web.browse_items')
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        @lang('campusfind_web_web::app.web.hero.subheadline')
                    </p>
                </div>

                <div class="mt-6">
                    <a
                        href="{{ route('campusfind_web.web.items.index') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-[var(--brand-color)] px-5 py-2.5 text-xs font-bold text-white shadow hover:opacity-90 text-decoration-none transition-opacity"
                    >
                        @lang('campusfind_web_web::app.web.browse_items') &rarr;
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-200 dark:border-gray-800 p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="h-10 w-10 rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-950 dark:text-amber-400 flex items-center justify-center mb-3">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        @lang('campusfind_web_web::app.web.how_it_works')
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Learn how to claim found items and verify custody.
                    </p>
                </div>

                <div class="mt-6">
                    <a
                        href="{{ route('campusfind_web.web.pages.show', ['page' => 'how-it-works']) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-50 text-decoration-none shadow-sm transition-colors"
                    >
                        @lang('campusfind_web_web::app.web.how_it_works') &rarr;
                    </a>
                </div>
            </div>
        </div>
    </x-campusfind_web_web::container>
</x-campusfind_web_web::layouts>
