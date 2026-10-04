<x-web::layouts>
    <x-web::section class="py-10">
        <div class="mb-10">
            <x-web::page-header
                :title="trans('campusfind_web_web::app.web.browse.title')"
                :description="trans('campusfind_web_web::app.web.browse.subtitle')"
            />

            @if ($hasFilterErrors)
                <x-web::alert variant="warning" class="mt-6">
                    @lang('campusfind_web_web::app.web.browse.invalid_filters')
                </x-web::alert>
            @endif

            <x-web::campus.report-filters
                class="mt-8"
                :filters="$filters"
                :categories="$categories"
                :action="route('campusfind_web.web.items.index')"
                :reset-url="route('campusfind_web.web.items.index', request()->filled('locale') ? ['locale' => request()->query('locale')] : [])"
            />

            <div class="mt-5 hidden items-center gap-3 rounded-xl bg-[#e6f4ee] px-4 py-3 text-sm font-bold text-[#185c54] dark:bg-[#185c54]/30 dark:text-[#a3e4c8]" role="status" aria-live="polite" aria-hidden="true" data-unified-loading>
                <x-web::spinner size="sm" />
                <span>@lang('campusfind_web_web::app.web.browse.loading')</span>
            </div>
        </div>

        <p class="mb-5 text-sm font-semibold text-slate-500 dark:text-slate-400" aria-live="polite">
            {{ trans_choice('campusfind_web_web::app.web.browse.results_count', $searchResult->total, ['count' => $searchResult->total]) }}
        </p>

        @if ($searchResult->isNotEmpty())
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($searchResult->items as $record)
                    <x-web::campus.report-card :record="$record" />
                @endforeach
            </div>

            @if ($searchResult->hasPages())
                <x-web::pagination
                    :current="$searchResult->currentPage"
                    :last="$searchResult->lastPage"
                    :previous-url="$searchResult->previousPage() ? route('campusfind_web.web.items.index', array_merge(request()->query(), ['page' => $searchResult->previousPage()])) : null"
                    :next-url="$searchResult->nextPage() ? route('campusfind_web.web.items.index', array_merge(request()->query(), ['page' => $searchResult->nextPage()])) : null"
                    :label="trans('campusfind_web_web::app.web.browse.pagination_label')"
                    :page-label="trans('campusfind_web_web::app.web.browse.page_of', ['current' => $searchResult->currentPage, 'last' => $searchResult->lastPage])"
                    :previous-label="trans('campusfind_web_web::app.web.browse.previous')"
                    :next-label="trans('campusfind_web_web::app.web.browse.next')"
                />
            @endif
        @else
            <x-web::empty-state
                :title="trans('campusfind_web_web::app.web.browse.no_results')"
                :description="trans('campusfind_web_web::app.web.browse.no_results_hint')"
            />
        @endif
    </x-web::section>
</x-web::layouts>
