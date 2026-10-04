<x-admin::layouts>
    <x-slot:title>@lang('lost_found::app.employee.matches.title')</x-slot>

    <div class="flex flex-col gap-4">
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <x-admin::breadcrumbs name="admin.lost_found.matches.index" />
            <div class="mt-2 text-xl font-bold dark:text-white">@lang('lost_found::app.employee.matches.title')</div>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">@lang('lost_found::app.employee.matches.disclaimer')</p>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <form method="GET" class="grid gap-3 md:grid-cols-4">
                <input class="control" type="number" min="1" name="lost_report_id" value="{{ $filters['lost_report_id'] ?? '' }}" placeholder="@lang('lost_found::app.employee.matches.lost_report_id')">
                <input class="control" type="number" min="1" name="found_item_id" value="{{ $filters['found_item_id'] ?? '' }}" placeholder="@lang('lost_found::app.employee.matches.found_item_id')">
                <select class="control" name="status">
                    <option value="">@lang('lost_found::app.employee.matches.all_statuses')</option>
                    @foreach (['suggested', 'reviewed', 'rejected', 'verified'] as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>@lang("lost_found::app.employee.matches.statuses.$status")</option>
                    @endforeach
                </select>
                <select class="control" name="sort">
                    @foreach (['score', 'newest', 'oldest'] as $sort)
                        <option value="{{ $sort }}" @selected(($filters['sort'] ?? 'score') === $sort)>@lang("lost_found::app.employee.matches.sorts.$sort")</option>
                    @endforeach
                </select>
                <button class="primary-button justify-center md:col-span-4" type="submit">@lang('lost_found::app.employee.matches.filter')</button>
            </form>

            @if ($canGenerate && isset($filters['lost_report_id']))
                <form method="POST" action="{{ route('admin.lost_found.matches.generate_report', $filters['lost_report_id']) }}" class="mt-3">
                    @csrf
                    <button class="secondary-button" type="submit">@lang('lost_found::app.employee.matches.generate_for_report')</button>
                </form>
            @elseif ($canGenerate && isset($filters['found_item_id']))
                <form method="POST" action="{{ route('admin.lost_found.matches.generate_item', $filters['found_item_id']) }}" class="mt-3">
                    @csrf
                    <button class="secondary-button" type="submit">@lang('lost_found::app.employee.matches.generate_for_item')</button>
                </form>
            @endif
        </div>

        @forelse ($matches as $match)
            @php
                $snapshot = $match->latestSnapshot;
                $status = $match->lifecycleStatus();
                $signals = $snapshot?->signals ?? [];
            @endphp
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                    <div class="grid flex-1 gap-4 md:grid-cols-2">
                        <section>
                            <div class="text-xs font-semibold uppercase text-rose-600">@lang('lost_found::app.employee.matches.lost_report')</div>
                            <div class="font-bold text-gray-900 dark:text-white">{{ $match->lostReport?->public_reference }} — {{ $match->lostReport?->title }}</div>
                            <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $match->lostReport?->lost_location }} · {{ $match->lostReport?->lost_at?->format('Y-m-d H:i') }}</div>
                            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ $match->lostReport?->public_description }}</p>
                        </section>
                        <section>
                            <div class="text-xs font-semibold uppercase text-emerald-600">@lang('lost_found::app.employee.matches.found_item')</div>
                            <div class="font-bold text-gray-900 dark:text-white">{{ $match->foundItem?->public_reference }} — {{ $match->foundItem?->title }}</div>
                            <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $match->foundItem?->found_location }} · {{ $match->foundItem?->found_at?->format('Y-m-d H:i') }}</div>
                            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ $match->foundItem?->public_description }}</p>
                        </section>
                    </div>
                    <div class="min-w-44 rounded-md bg-gray-50 p-3 text-center dark:bg-gray-800">
                        <div class="text-xs text-gray-500">@lang('lost_found::app.employee.matches.ranking_score')</div>
                        <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ $snapshot ? number_format($snapshot->score_basis_points / 100, 2) : '—' }} / 100</div>
                        <div class="mt-1 text-xs font-semibold">@lang("lost_found::app.employee.matches.statuses.$status")</div>
                    </div>
                </div>

                <div class="mt-4 grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach (['category', 'description', 'location', 'temporal'] as $signal)
                        <div class="rounded border border-gray-200 p-2 text-sm dark:border-gray-700">
                            <div class="font-semibold">@lang("lost_found::app.employee.matches.signals.$signal")</div>
                            <div class="text-gray-600 dark:text-gray-400">
                                @if (isset($signals[$signal]['explanation']))
                                    @lang('lost_found::app.employee.matches.explanations.'.$signals[$signal]['explanation'])
                                @else
                                    @lang('lost_found::app.employee.matches.not_evaluated')
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($status !== 'verified' && $status !== 'rejected')
                    <div class="mt-4 grid gap-3 lg:grid-cols-2">
                        @if ($canReview)
                            <form method="POST" action="{{ route('admin.lost_found.matches.review', $match->id) }}" class="flex flex-col gap-2 rounded border border-gray-200 p-3 dark:border-gray-700">
                                @csrf
                                <select class="control" name="decision" required>
                                    <option value="reviewed">@lang('lost_found::app.employee.matches.mark_reviewed')</option>
                                    <option value="rejected">@lang('lost_found::app.employee.matches.reject')</option>
                                </select>
                                <textarea class="control" name="notes" maxlength="2000" placeholder="@lang('lost_found::app.employee.matches.review_notes')"></textarea>
                                <button class="secondary-button justify-center" type="submit">@lang('lost_found::app.employee.matches.save_review')</button>
                            </form>
                        @endif
                        @if ($canVerify)
                            <form method="POST" action="{{ route('admin.lost_found.matches.verify', $match->id) }}" class="flex flex-col gap-2 rounded border border-amber-200 bg-amber-50 p-3 dark:border-amber-900 dark:bg-amber-950/20">
                                @csrf
                                <p class="text-xs text-amber-800 dark:text-amber-300">@lang('lost_found::app.employee.matches.verify_warning')</p>
                                <textarea class="control" name="verification_evidence" maxlength="4000" required placeholder="@lang('lost_found::app.employee.matches.verification_evidence')"></textarea>
                                <button class="primary-button justify-center" type="submit">@lang('lost_found::app.employee.matches.verify_explicitly')</button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        @empty
            <div class="rounded-lg border border-gray-200 bg-white p-8 text-center text-gray-500 dark:border-gray-800 dark:bg-gray-900">@lang('lost_found::app.employee.matches.empty')</div>
        @endforelse

        {{ $matches->links() }}
    </div>
</x-admin::layouts>
