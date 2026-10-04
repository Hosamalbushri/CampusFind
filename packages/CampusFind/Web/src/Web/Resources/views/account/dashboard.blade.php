<x-web::layouts>
    <x-web::container size="md" class="py-10 sm:py-12">
        <!-- Breadcrumbs Navigation -->
        <x-web::breadcrumbs :items="[trans('campusfind_web_web::app.web.auth.dashboard') => '']" class="mb-8" />

        <!-- Flash Session Alerts -->
        <x-web::flash-group class="mb-8" />

        <!-- Student Profile Card -->
        <x-web::card variant="elevated" padding="lg" class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 border-b border-slate-100 dark:border-slate-800 pb-6">
                <div class="flex items-center gap-4">
                    <x-web::avatar :name="$user->name ?? 'Student User'" size="xl" />

                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                                {{ $user->name ?? 'Student User' }}
                            </h1>
                            <x-web::badge variant="mint" size="sm" :dot="true">
                                @lang('campusfind_web_web::app.web.auth.active_student')
                            </x-web::badge>
                        </div>
                        <p class="text-sm font-mono text-slate-500 dark:text-slate-400 mt-1">
                            @lang('campusfind_web_web::app.web.auth.card_number'): <span class="font-bold text-slate-800 dark:text-slate-200">{{ $user->university_card_number ?? '-' }}</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <x-web::form :action="route('campusfind_web.web.logout')" method="POST" class="m-0 space-y-0">
                        <x-web::button
                            type="submit"
                            variant="ghost"
                            size="sm"
                            class="text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 hover:text-rose-700"
                        >
                            @lang('campusfind_web_web::app.web.auth.logout')
                        </x-web::button>
                    </x-web::form>
                </div>
            </div>

            <!-- Student Academic Details -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 pt-6 text-sm">
                <x-web::card variant="flat" padding="sm">
                    <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 block mb-1">@lang('campusfind_web_web::app.web.auth.registration_number')</span>
                    <span class="font-bold text-slate-900 dark:text-slate-100 font-mono">{{ $user->registration_number ?? '-' }}</span>
                </x-web::card>

                <x-web::card variant="flat" padding="sm">
                    <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 block mb-1">@lang('campusfind_web_web::app.web.auth.major')</span>
                    <span class="font-bold text-slate-900 dark:text-slate-100">{{ $user->major ?? '-' }}</span>
                </x-web::card>

                <x-web::card variant="flat" padding="sm">
                    <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 block mb-1">@lang('campusfind_web_web::app.web.auth.academic_level')</span>
                    <span class="font-bold text-slate-900 dark:text-slate-100">{{ $user->academic_level ?? '-' }}</span>
                </x-web::card>
            </div>
        </x-web::card>

        <!-- Quick Action Shortcuts -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-8">
            <x-web::card variant="interactive" padding="md" class="flex flex-col justify-between">
                <div>
                    <div class="h-10 w-10 rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 flex items-center justify-center mb-3 ring-4 ring-rose-50 dark:ring-rose-950/20">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white">
                        @lang('campusfind_web_web::app.web.reports.report_lost')
                    </h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        @lang('campusfind_web_web::app.web.reports.report_lost_subtitle')
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <x-web::button
                        href="{{ route('campusfind_web.web.reports.lost') }}"
                        variant="primary"
                        size="sm"
                        class="w-full"
                    >
                        @lang('campusfind_web_web::app.web.reports.report_lost')
                    </x-web::button>
                </div>
            </x-web::card>

            <x-web::card variant="interactive" padding="md" class="flex flex-col justify-between">
                <div>
                    <div class="h-10 w-10 rounded-xl bg-[#e6f4ee] text-[#185c54] dark:bg-[#185c54]/30 dark:text-[#a3e4c8] flex items-center justify-center mb-3 ring-4 ring-[#e6f4ee]/60 dark:ring-[#185c54]/10">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white">
                        @lang('campusfind_web_web::app.web.reports.report_found')
                    </h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        @lang('campusfind_web_web::app.web.reports.report_found_subtitle')
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <x-web::button
                        href="{{ route('campusfind_web.web.reports.found') }}"
                        variant="mint"
                        size="sm"
                        class="w-full"
                    >
                        @lang('campusfind_web_web::app.web.reports.report_found')
                    </x-web::button>
                </div>
            </x-web::card>

            <x-web::card variant="interactive" padding="md" class="flex flex-col justify-between">
                <div>
                    <div class="h-10 w-10 rounded-xl bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 flex items-center justify-center mb-3">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white">
                        @lang('campusfind_web_web::app.web.browse_items')
                    </h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        @lang('campusfind_web_web::app.web.hero.subheadline')
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <x-web::button
                        href="{{ route('campusfind_web.web.items.index') }}"
                        variant="secondary"
                        size="sm"
                        class="w-full"
                    >
                        @lang('campusfind_web_web::app.web.browse_items')
                    </x-web::button>
                </div>
            </x-web::card>
        </div>

        <!-- Student Dashboard Activity Tabs -->
        <x-web::card variant="elevated" padding="lg">
            <x-web::tabs position="start">
                <!-- Tab 1: My Lost Reports -->
                <x-web::tabs.item :title="trans('campusfind_web_web::app.web.reports.my_reports') . ' (' . count($reports ?? []) . ')'" :isSelected="true">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">
                                @lang('campusfind_web_web::app.web.reports.my_reports')
                            </h2>
                        </div>
                        <x-web::button
                            href="{{ route('campusfind_web.web.reports.lost') }}"
                            variant="primary"
                            size="sm"
                        >
                            + @lang('campusfind_web_web::app.web.reports.report_lost')
                        </x-web::button>
                    </div>

                    @if (! empty($reports) && count($reports) > 0)
                        <div class="space-y-4">
                            @foreach ($reports as $report)
                                <x-web::card variant="flat" padding="md" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="space-y-1.5">
                                        <div class="flex items-center gap-2.5 flex-wrap">
                                            <h4 class="font-bold text-base text-slate-900 dark:text-white">
                                                {{ $report->title }}
                                            </h4>
                                            <x-web::badge variant="neutral" size="xs" class="font-mono">
                                                {{ $report->public_reference }}
                                            </x-web::badge>
                                            @if ($report->category)
                                                <x-web::badge variant="mint" size="xs">
                                                    {{ $report->category->name }}
                                                </x-web::badge>
                                            @endif
                                            <x-web::badge variant="success" size="xs" :dot="true">
                                                {{ is_object($report->status) ? $report->status->value : $report->status }}
                                            </x-web::badge>
                                        </div>

                                        @if ($report->lost_location || $report->lost_at)
                                            <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-3">
                                                @if ($report->lost_location)
                                                    <span>📍 {{ $report->lost_location }}</span>
                                                @endif
                                                @if ($report->lost_at)
                                                    <span>📅 {{ $report->lost_at->format('Y-m-d H:i') }}</span>
                                                @endif
                                            </p>
                                        @endif
                                    </div>
                                </x-web::card>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10 text-slate-500 dark:text-slate-400">
                            <svg class="h-12 w-12 mx-auto text-slate-300 dark:text-slate-600 mb-3 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-sm font-medium">@lang('campusfind_web_web::app.web.reports.no_reports_yet')</p>
                            <div class="mt-4">
                                <x-web::button
                                    href="{{ route('campusfind_web.web.reports.lost') }}"
                                    variant="mint"
                                    size="sm"
                                >
                                    @lang('campusfind_web_web::app.web.reports.report_lost')
                                </x-web::button>
                            </div>
                        </div>
                    @endif
                </x-web::tabs.item>

                <!-- Tab 2: My Found Reports -->
                <x-web::tabs.item :title="trans('campusfind_web_web::app.web.reports.my_found_reports') . ' (' . count($foundReports ?? []) . ')'" :isSelected="false">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">
                                @lang('campusfind_web_web::app.web.reports.my_found_reports')
                            </h2>
                        </div>
                        <x-web::button
                            href="{{ route('campusfind_web.web.reports.found') }}"
                            variant="mint"
                            size="sm"
                        >
                            + @lang('campusfind_web_web::app.web.reports.report_found')
                        </x-web::button>
                    </div>

                    @forelse ($foundReports as $foundReport)
                        <div class="flex flex-col gap-3 border-b border-slate-100 py-4 last:border-0 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-bold text-slate-900 dark:text-white">{{ $foundReport->title }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $foundReport->public_reference }} · {{ $foundReport->status->value }}</p>
                            </div>
                            @if (in_array($foundReport->status->value, ['reported', 'in_custody'], true))
                                <x-web::button href="{{ route('campusfind_web.web.items.show', $foundReport->public_reference) }}" variant="ghost" size="sm">
                                    @lang('campusfind_web_web::app.web.view_details')
                                </x-web::button>
                            @endif
                        </div>
                    @empty
                        <p class="py-6 text-sm text-slate-500 dark:text-slate-400">@lang('campusfind_web_web::app.web.reports.no_found_reports_yet')</p>
                    @endforelse
                </x-web::tabs.item>

                <!-- Tab 3: My Responses -->
                <x-web::tabs.item :title="trans('campusfind_web_web::app.web.found_response.my_responses') . ' (' . count($foundResponses ?? []) . ')'" :isSelected="false">
                    <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight pb-4 border-b border-slate-100 dark:border-slate-800 mb-6">
                        @lang('campusfind_web_web::app.web.found_response.my_responses')
                    </h2>

                    @forelse ($foundResponses as $foundResponse)
                        <div class="flex flex-col gap-3 border-b border-slate-100 py-4 last:border-0 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-bold text-slate-900 dark:text-white">{{ $foundResponse->lostReport?->title }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $foundResponse->public_reference }} · @lang('campusfind_web_web::app.web.found_response.status_'.$foundResponse->status->value)</p>
                            </div>
                            @if ($foundResponse->lostReport?->status?->value === 'active')
                                <x-web::button href="{{ route('campusfind_web.web.lost-reports.show', $foundResponse->lostReport->public_reference) }}" variant="ghost" size="sm">
                                    @lang('campusfind_web_web::app.web.view_details')
                                </x-web::button>
                            @endif
                        </div>
                    @empty
                        <p class="py-6 text-sm text-slate-500 dark:text-slate-400">@lang('campusfind_web_web::app.web.found_response.no_responses_yet')</p>
                    @endforelse
                </x-web::tabs.item>

                <!-- Tab 4: My Claims -->
                <x-web::tabs.item :title="trans('campusfind_web_web::app.web.claims.my_claims') . ' (' . count($claims ?? []) . ')'" :isSelected="false">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">
                                @lang('campusfind_web_web::app.web.claims.my_claims')
                            </h2>
                        </div>
                        <x-web::button
                            href="{{ route('campusfind_web.web.items.index') }}"
                            variant="secondary"
                            size="sm"
                        >
                            @lang('campusfind_web_web::app.web.browse_items')
                        </x-web::button>
                    </div>

                    @if (! empty($claims) && count($claims) > 0)
                        <div class="space-y-4">
                            @foreach ($claims as $claim)
                                <x-web::card variant="flat" padding="md" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="space-y-1.5">
                                        <div class="flex items-center gap-2.5 flex-wrap">
                                            <h4 class="font-bold text-base text-slate-900 dark:text-white">
                                                {{ $claim->foundItem->title ?? ('Claim #' . $claim->id) }}
                                            </h4>
                                            @if ($claim->foundItem)
                                                <x-web::badge variant="neutral" size="xs" class="font-mono">
                                                    {{ $claim->foundItem->public_reference }}
                                                </x-web::badge>
                                                @if ($claim->foundItem->category)
                                                    <x-web::badge variant="mint" size="xs">
                                                        {{ $claim->foundItem->category->name }}
                                                    </x-web::badge>
                                                @endif
                                            @endif
                                            <x-web::badge variant="warning" size="xs" :dot="true">
                                                {{ is_object($claim->status) ? $claim->status->value : $claim->status }}
                                            </x-web::badge>
                                        </div>

                                        <div class="text-xs text-slate-400 flex items-center gap-3 pt-1">
                                            <span>📅 {{ $claim->created_at?->format('Y-m-d H:i') }}</span>
                                        </div>
                                    </div>

                                    @if ($claim->foundItem)
                                        <div class="flex items-center">
                                            <x-web::button
                                                href="{{ route('campusfind_web.web.items.show', $claim->foundItem->public_reference) }}"
                                                variant="ghost"
                                                size="sm"
                                            >
                                                @lang('campusfind_web_web::app.web.view_details')
                                            </x-web::button>
                                        </div>
                                    @endif
                                </x-web::card>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10 text-slate-500 dark:text-slate-400">
                            <svg class="h-12 w-12 mx-auto text-slate-300 dark:text-slate-600 mb-3 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="text-sm font-medium">@lang('campusfind_web_web::app.web.claims.no_claims_yet')</p>
                        </div>
                    @endif
                </x-web::tabs.item>
            </x-web::tabs>
        </x-web::card>
    </x-web::container>
</x-web::layouts>
