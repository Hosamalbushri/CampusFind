<x-campusfind_web_web::layouts>
    <x-campusfind_web_web::section class="py-10">
        <!-- Back Link -->
        <div class="mb-6">
            <a
                href="{{ route('campusfind_web.web.items.index') }}"
                class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-[var(--brand-color)] dark:text-gray-400 dark:hover:text-white text-decoration-none transition-colors"
            >
                <svg class="h-4 w-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span>@lang('campusfind_web_web::app.web.back_to_items')</span>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Images Column -->
            <div class="lg:col-span-6 space-y-4">
                <div class="relative h-96 w-full rounded-2xl bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-800 overflow-hidden flex items-center justify-center">
                    @if ($item->hasImage && $item->imageUrl)
                        <img src="{{ $item->imageUrl }}" alt="{{ $item->title }}" class="h-full w-full object-contain">
                    @else
                        <div class="text-gray-400 dark:text-gray-600 flex flex-col items-center">
                            <svg class="h-16 w-16 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm mt-2">@lang('campusfind_web_web::app.web.images')</span>
                        </div>
                    @endif
                </div>

                @if (! empty($item->additionalImages))
                    <div class="grid grid-cols-4 gap-3">
                        @foreach ($item->additionalImages as $additionalUrl)
                            <div class="h-24 rounded-xl bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-800 overflow-hidden">
                                <img src="{{ $additionalUrl }}" alt="Additional image" class="h-full w-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Details Column -->
            <div class="lg:col-span-6 flex flex-col justify-between">
                <div>
                    <!-- Badges -->
                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        @if ($item->category)
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300">
                                {{ $item->category }}
                            </span>
                        @endif

                        <span class="px-3 py-1 text-xs font-mono font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300">
                            {{ $item->reference }}
                        </span>
                    </div>

                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">
                        {{ $item->title }}
                    </h1>

                    @if ($item->description)
                        <div class="mt-4 text-base text-gray-600 dark:text-gray-300 leading-relaxed">
                            {{ $item->description }}
                        </div>
                    @endif

                    <!-- Details Table -->
                    <div class="mt-6 border-t border-b border-gray-200 dark:border-gray-800 py-4 space-y-3">
                        @if ($item->foundLocation)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">@lang('campusfind_web_web::app.web.found_location'):</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $item->foundLocation }}</span>
                            </div>
                        @endif

                        @if ($item->foundAt)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">@lang('campusfind_web_web::app.web.found_date'):</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $item->foundAt->format('Y-m-d H:i') }}</span>
                            </div>
                        @endif

                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">@lang('campusfind_web_web::app.web.reference'):</span>
                            <span class="font-mono font-semibold text-gray-900 dark:text-white">{{ $item->reference }}</span>
                        </div>
                    </div>
                </div>

                <!-- Claim Action Card -->
                <div class="mt-8 rounded-2xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900/50 p-6">
                    <h3 class="text-lg font-bold text-blue-900 dark:text-blue-100">
                        @lang('campusfind_web_web::app.web.claim_item')
                    </h3>
                    <p class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                        @lang('campusfind_web_web::app.web.hero.subheadline')
                    </p>

                    <div class="mt-4">
                        @if (\Illuminate\Support\Facades\Route::has('student.login'))
                            <a
                                href="{{ route('student.login') }}"
                                class="inline-flex items-center justify-center w-full sm:w-auto rounded-xl bg-[var(--brand-color)] px-6 py-3 text-sm font-bold text-white shadow hover:opacity-90 text-decoration-none transition-opacity"
                            >
                                @lang('campusfind_web_web::app.web.claim_item')
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </x-campusfind_web_web::section>
</x-campusfind_web_web::layouts>
