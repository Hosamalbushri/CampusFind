<x-campusfind_web_web::layouts>
    <div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white dark:bg-gray-900 p-8 sm:p-10 rounded-3xl border border-gray-200 dark:border-gray-800 shadow-xl">
            <!-- Header -->
            <div class="text-center">
                <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-100 text-[var(--brand-color)] dark:bg-blue-950 dark:text-blue-400 mb-4 shadow-sm">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                    </svg>
                </div>

                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                    @lang('campusfind_web_web::app.web.auth.login_title')
                </h2>

                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    @lang('campusfind_web_web::app.web.auth.login_subtitle')
                </p>
            </div>

            <!-- Errors Alert -->
            @if ($errors->any())
                <div class="rounded-xl bg-red-50 dark:bg-red-950/50 p-4 border border-red-200 dark:border-red-900/50 text-sm text-red-700 dark:text-red-300">
                    <div class="flex items-center gap-2 font-semibold mb-1">
                        <svg class="h-5 w-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>@lang('campusfind_web_web::app.web.auth.login_error_title')</span>
                    </div>
                    <ul class="list-disc ltr:list-inside rtl:list-inside space-y-1 text-xs mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="rounded-xl bg-green-50 dark:bg-green-950/50 p-4 border border-green-200 dark:border-green-900/50 text-sm text-green-700 dark:text-green-300">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Form -->
            <form class="mt-8 space-y-5" action="{{ route('campusfind_web.web.login.store') }}" method="POST">
                @csrf

                <!-- University Card Number -->
                <div>
                    <label for="university_card_number" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        @lang('campusfind_web_web::app.web.auth.card_number')
                    </label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 ltr:left-0 rtl:right-0 ltr:pl-3.5 rtl:pr-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                            </svg>
                        </div>
                        <input
                            id="university_card_number"
                            name="university_card_number"
                            type="text"
                            required
                            autocomplete="username"
                            value="{{ old('university_card_number') }}"
                            placeholder="@lang('campusfind_web_web::app.web.auth.card_number_placeholder')"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 ltr:pl-10 rtl:pr-10 pr-4 py-3 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[var(--brand-color)] focus:border-transparent transition-all"
                        />
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        @lang('campusfind_web_web::app.web.auth.password')
                    </label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 ltr:left-0 rtl:right-0 ltr:pl-3.5 rtl:pr-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 ltr:pl-10 rtl:pr-10 pr-10 py-3 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[var(--brand-color)] focus:border-transparent transition-all"
                        />
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer select-none text-sm text-gray-600 dark:text-gray-400">
                        <input
                            type="checkbox"
                            name="remember"
                            id="remember"
                            class="h-4 w-4 rounded border-gray-300 dark:border-gray-700 text-[var(--brand-color)] focus:ring-[var(--brand-color)]"
                        />
                        <span>@lang('campusfind_web_web::app.web.auth.remember')</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div>
                    <button
                        type="submit"
                        class="w-full flex justify-center py-3.5 px-4 rounded-xl shadow-md text-sm font-bold text-white bg-[var(--brand-color)] hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--brand-color)] transition-all cursor-pointer border-0"
                    >
                        @lang('campusfind_web_web::app.web.auth.submit')
                    </button>
                </div>

                <!-- Back to Home -->
                <div class="text-center pt-2">
                    <a
                        href="{{ route('campusfind_web.web.home') }}"
                        class="text-xs font-semibold text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 text-decoration-none transition-colors"
                    >
                        &larr; @lang('campusfind_web_web::app.web.auth.back_to_portal')
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-campusfind_web_web::layouts>
