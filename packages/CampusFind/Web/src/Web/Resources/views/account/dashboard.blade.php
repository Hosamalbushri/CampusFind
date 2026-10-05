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
                    <x-web::form method="POST" :action="route('campusfind_web.web.logout')" id="dashboardLogoutForm" class="hidden"></x-web::form>

                    <x-web::button
                        type="button"
                        variant="outline"
                        size="sm"
                        onclick="window.app.config.globalProperties.$emitter.emit('open-confirm-modal', {
                            title: '{{ trans('campusfind_web_web::app.web.auth.logout_confirm_title') }}',
                            message: '{{ trans('campusfind_web_web::app.web.auth.logout_confirm_desc') }}',
                            options: {
                                btnDisagree: '{{ trans('campusfind_web_web::app.web.cancel') }}',
                                btnAgree: '{{ trans('campusfind_web_web::app.web.auth.logout') }}'
                            },
                            agree: () => { document.getElementById('dashboardLogoutForm').submit(); }
                        })"
                        class="border-rose-200 text-rose-600 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-300 dark:border-rose-900/60 dark:text-rose-400 dark:hover:bg-rose-950/40 dark:hover:border-rose-800 transition-all font-bold gap-2 shadow-2xs cursor-pointer"
                    >
                        <svg class="h-4 w-4 rtl:rotate-180 shrink-0 text-rose-500 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>@lang('campusfind_web_web::app.web.auth.logout')</span>
                    </x-web::button>
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
                    <div class="flex items-center gap-3 mb-3">
                        <div class="h-10 w-10 shrink-0 rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 flex items-center justify-center ring-4 ring-rose-50 dark:ring-rose-950/20">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-black text-slate-900 dark:text-white">
                            @lang('campusfind_web_web::app.web.reports.report_lost')
                        </h3>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
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
                    <div class="flex items-center gap-3 mb-3">
                        <div class="h-10 w-10 shrink-0 rounded-xl bg-[#e6f4ee] text-[#185c54] dark:bg-[#185c54]/30 dark:text-[#a3e4c8] flex items-center justify-center ring-4 ring-[#e6f4ee]/60 dark:ring-[#185c54]/10">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-black text-slate-900 dark:text-white">
                            @lang('campusfind_web_web::app.web.reports.report_found')
                        </h3>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
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
                    <div class="flex items-center gap-3 mb-3">
                        <div class="h-10 w-10 shrink-0 rounded-xl bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 flex items-center justify-center">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-black text-slate-900 dark:text-white">
                            @lang('campusfind_web_web::app.web.browse_items')
                        </h3>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
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
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 pb-4 border-b border-slate-100 dark:border-slate-800 mb-6">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">
                            @lang('campusfind_web_web::app.web.reports.my_reports')
                        </h2>
                        <x-web::button
                            href="{{ route('campusfind_web.web.reports.lost') }}"
                            variant="primary"
                            size="sm"
                            class="w-full sm:w-auto shrink-0 whitespace-nowrap"
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
                                            @php
                                                $repStatus = is_object($report->status) ? $report->status->value : (string) $report->status;
                                                $repVariant = match ($repStatus) {
                                                    'approved', 'resolved', 'returned', 'verified' => 'success',
                                                    'submitted', 'under_review', 'needs_information' => 'warning',
                                                    'active', 'reported', 'in_custody' => 'mint',
                                                    'rejected', 'disposed' => 'danger',
                                                    default => 'neutral',
                                                };
                                            @endphp
                                            <x-web::badge :variant="$repVariant" size="xs" :dot="true">
                                                @lang('campusfind_web_web::app.web.statuses.'.$repStatus)
                                            </x-web::badge>
                                        </div>

                                        @if ($report->lost_location || $report->lost_at)
                                            <div class="text-xs text-slate-500 dark:text-slate-400 flex flex-wrap items-center gap-x-4 gap-y-2 pt-1 font-medium">
                                                @if ($report->lost_location)
                                                    <div class="flex items-center gap-1.5 shrink-0">
                                                        <svg class="h-4 w-4 shrink-0 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                        <span>{{ $report->lost_location }}</span>
                                                    </div>
                                                @endif
                                                @if ($report->lost_at)
                                                    <div class="flex items-center gap-1.5 shrink-0">
                                                        <svg class="h-4 w-4 shrink-0 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                        <span>{{ $report->lost_at->format('Y-m-d H:i') }}</span>
                                                    </div>
                                                @endif
                                            </div>
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
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 pb-4 border-b border-slate-100 dark:border-slate-800 mb-6">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">
                            @lang('campusfind_web_web::app.web.reports.my_found_reports')
                        </h2>
                        <x-web::button
                            href="{{ route('campusfind_web.web.reports.found') }}"
                            variant="mint"
                            size="sm"
                            class="w-full sm:w-auto shrink-0 whitespace-nowrap"
                        >
                            + @lang('campusfind_web_web::app.web.reports.report_found')
                        </x-web::button>
                    </div>

                    @if (! empty($foundReports) && count($foundReports) > 0)
                        <div class="space-y-4">
                            @foreach ($foundReports as $foundReport)
                                <x-web::card variant="flat" padding="md" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="space-y-1.5">
                                        <div class="flex items-center gap-2.5 flex-wrap">
                                            <h4 class="font-bold text-base text-slate-900 dark:text-white">
                                                {{ $foundReport->title }}
                                            </h4>
                                            @php
                                                $fRepStatus = is_object($foundReport->status) ? $foundReport->status->value : (string) $foundReport->status;
                                                $fRepVariant = match ($fRepStatus) {
                                                    'approved', 'resolved', 'returned', 'verified' => 'success',
                                                    'submitted', 'under_review', 'needs_information' => 'warning',
                                                    'active', 'reported', 'in_custody' => 'mint',
                                                    'rejected', 'disposed' => 'danger',
                                                    default => 'neutral',
                                                };
                                            @endphp
                                            <x-web::badge :variant="$fRepVariant" size="xs" :dot="true">
                                                @lang('campusfind_web_web::app.web.statuses.'.$fRepStatus)
                                            </x-web::badge>
                                        </div>
                                        @if ($foundReport->found_location || $foundReport->found_at)
                                            <div class="text-xs text-slate-500 dark:text-slate-400 flex flex-wrap items-center gap-x-4 gap-y-2 pt-1 font-medium">
                                                @if ($foundReport->found_location)
                                                    <div class="flex items-center gap-1.5 shrink-0">
                                                        <svg class="h-4 w-4 shrink-0 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                        <span>{{ $foundReport->found_location }}</span>
                                                    </div>
                                                @endif
                                                @if ($foundReport->found_at)
                                                    <div class="flex items-center gap-1.5 shrink-0">
                                                        <svg class="h-4 w-4 shrink-0 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                        <span>{{ $foundReport->found_at->format('Y-m-d H:i') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    @php
                                        $fRepStatusVal = is_object($foundReport->status) ? $foundReport->status->value : (string) $foundReport->status;
                                    @endphp
                                    @if (in_array($fRepStatusVal, ['reported', 'in_custody'], true) && ! in_array($fRepStatusVal, ['returned', 'resolved', 'cancelled', 'disposed'], true))
                                        <div>
                                            <x-web::button href="{{ route('campusfind_web.web.items.show', $foundReport->public_reference) }}" variant="ghost" size="sm">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm font-medium">@lang('campusfind_web_web::app.web.reports.no_found_reports_yet')</p>
                            <div class="mt-4">
                                <x-web::button
                                    href="{{ route('campusfind_web.web.reports.found') }}"
                                    variant="mint"
                                    size="sm"
                                >
                                    @lang('campusfind_web_web::app.web.reports.report_found')
                                </x-web::button>
                            </div>
                        </div>
                    @endif
                </x-web::tabs.item>

                <!-- Tab 3: My Responses -->
                <x-web::tabs.item :title="trans('campusfind_web_web::app.web.found_response.my_responses') . ' (' . count($foundResponses ?? []) . ')'" :isSelected="false">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 pb-4 border-b border-slate-100 dark:border-slate-800 mb-6">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">
                            @lang('campusfind_web_web::app.web.found_response.my_responses')
                        </h2>
                    </div>

                    @if (! empty($foundResponses) && count($foundResponses) > 0)
                        <div class="space-y-4">
                            @foreach ($foundResponses as $foundResponse)
                                <x-web::card variant="flat" padding="md" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="space-y-1.5">
                                        <div class="flex items-center gap-2.5 flex-wrap">
                                            <h4 class="font-bold text-base text-slate-900 dark:text-white">
                                                {{ $foundResponse->lostReport?->title ?? trans('campusfind_web_web::app.web.found_response.title') }}
                                            </h4>
                                            @php
                                                $respStatus = is_object($foundResponse->status) ? $foundResponse->status->value : (string) $foundResponse->status;
                                                $respVariant = match ($respStatus) {
                                                    'approved', 'resolved', 'returned', 'verified' => 'success',
                                                    'submitted', 'under_review', 'needs_information' => 'warning',
                                                    'active', 'reported', 'in_custody' => 'mint',
                                                    'rejected', 'disposed' => 'danger',
                                                    default => 'neutral',
                                                };
                                            @endphp
                                            <x-web::badge :variant="$respVariant" size="xs" :dot="true">
                                                @lang('campusfind_web_web::app.web.statuses.'.$respStatus)
                                            </x-web::badge>
                                        </div>
                                        @if ($foundResponse->found_location || $foundResponse->found_at)
                                            <div class="text-xs text-slate-500 dark:text-slate-400 flex flex-wrap items-center gap-x-4 gap-y-2 pt-1 font-medium">
                                                @if ($foundResponse->found_location)
                                                    <div class="flex items-center gap-1.5 shrink-0">
                                                        <svg class="h-4 w-4 shrink-0 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                        <span>{{ $foundResponse->found_location }}</span>
                                                    </div>
                                                @endif
                                                @if ($foundResponse->found_at)
                                                    <div class="flex items-center gap-1.5 shrink-0">
                                                        <svg class="h-4 w-4 shrink-0 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                        <span>{{ $foundResponse->found_at->format('Y-m-d H:i') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    @php
                                        $respReportStatus = $foundResponse->lostReport ? (is_object($foundResponse->lostReport->status) ? $foundResponse->lostReport->status->value : (string) $foundResponse->lostReport->status) : null;
                                    @endphp
                                    @if ($foundResponse->lostReport && ! in_array($respReportStatus, ['returned', 'resolved', 'cancelled', 'disposed'], true))
                                        <div>
                                            <x-web::button href="{{ route('campusfind_web.web.lost-reports.show', $foundResponse->lostReport->public_reference) }}" variant="ghost" size="sm">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <p class="text-sm font-medium">@lang('campusfind_web_web::app.web.found_response.no_responses_yet')</p>
                        </div>
                    @endif
                </x-web::tabs.item>

                <!-- Tab 4: My Claims -->
                <x-web::tabs.item :title="trans('campusfind_web_web::app.web.claims.my_claims') . ' (' . count($claims ?? []) . ')'" :isSelected="false">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 pb-4 border-b border-slate-100 dark:border-slate-800 mb-6">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">
                            @lang('campusfind_web_web::app.web.claims.my_claims')
                        </h2>
                        <x-web::button
                            href="{{ route('campusfind_web.web.items.index') }}"
                            variant="secondary"
                            size="sm"
                            class="w-full sm:w-auto shrink-0 whitespace-nowrap"
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
                                            @php
                                                $claimStatus = is_object($claim->status) ? $claim->status->value : (string) $claim->status;
                                                $claimVariant = match ($claimStatus) {
                                                    'approved', 'resolved', 'returned', 'verified' => 'success',
                                                    'submitted', 'under_review', 'needs_information' => 'warning',
                                                    'active', 'reported', 'in_custody' => 'mint',
                                                    'rejected', 'disposed' => 'danger',
                                                    default => 'neutral',
                                                };
                                            @endphp
                                            <x-web::badge :variant="$claimVariant" size="xs" :dot="true">
                                                @lang('campusfind_web_web::app.web.statuses.'.$claimStatus)
                                            </x-web::badge>
                                        </div>

                                        @if ($claim->created_at)
                                            <div class="text-xs text-slate-500 dark:text-slate-400 flex flex-wrap items-center gap-x-4 gap-y-2 pt-1 font-medium">
                                                <div class="flex items-center gap-1.5 shrink-0">
                                                    <svg class="h-4 w-4 shrink-0 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <span>{{ $claim->created_at->format('Y-m-d H:i') }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    @php
                                        $claimItemStatus = $claim->foundItem ? (is_object($claim->foundItem->status) ? $claim->foundItem->status->value : (string) $claim->foundItem->status) : null;
                                    @endphp
                                    @if ($claim->foundItem && ! in_array($claimItemStatus, ['returned', 'resolved', 'cancelled', 'disposed'], true))
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
