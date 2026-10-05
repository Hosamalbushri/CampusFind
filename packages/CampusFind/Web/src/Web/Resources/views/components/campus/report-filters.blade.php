@props([
    'filters',
    'categories',
    'action',
    'resetUrl',
])

@php
    $hasActiveAccordionFilters = !empty($filters['category'])
        || !empty($filters['location'])
        || !empty($filters['status'])
        || (!empty($filters['sort']) && $filters['sort'] !== 'newest')
        || !empty($filters['date_from'])
        || !empty($filters['date_to']);

    $activeFiltersCount = 0;
    if (!empty($filters['category'])) $activeFiltersCount++;
    if (!empty($filters['location'])) $activeFiltersCount++;
    if (!empty($filters['status'])) $activeFiltersCount++;
    if (!empty($filters['sort']) && $filters['sort'] !== 'newest') $activeFiltersCount++;
    if (!empty($filters['date_from'])) $activeFiltersCount++;
    if (!empty($filters['date_to'])) $activeFiltersCount++;
@endphp

<x-web::form
    method="GET"
    :action="$action"
    {{ $attributes->merge(['class' => 'space-y-4 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900 sm:p-5 font-cairo']) }}
    data-unified-search-form
>
    @if (request()->filled('locale'))
        <x-web::form.control-group.control type="hidden" name="locale" :value="request()->query('locale')" />
    @endif

    <!-- Top Primary Search Bar (Type Switcher + Search Query + Action Buttons) -->
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
        <!-- Segmented Type Selector -->
        <fieldset class="shrink-0">
            <legend class="sr-only">@lang('campusfind_web_web::app.web.browse.type_label')</legend>
            <div class="inline-flex h-11 items-center rounded-xl bg-slate-100/90 p-1 dark:bg-slate-800/90 border border-slate-200/60 dark:border-slate-700/60">
                @foreach (['all', 'lost', 'found'] as $type)
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="{{ $type }}" class="peer sr-only" {{ $filters['type'] === $type ? 'checked' : '' }}>
                        <span class="inline-flex h-9 items-center justify-center rounded-lg px-3.5 text-xs font-bold text-slate-600 transition-all peer-checked:bg-white peer-checked:text-[#185c54] peer-checked:shadow-xs dark:text-slate-300 dark:peer-checked:bg-[#185c54] dark:peer-checked:text-white">
                            @lang('campusfind_web_web::app.web.browse.type_'.$type)
                        </span>
                    </label>
                @endforeach
            </div>
        </fieldset>

        <!-- Search Query Input -->
        <div class="relative flex-1">
            <div class="pointer-events-none absolute inset-y-0 ltr:left-0 rtl:right-0 flex items-center ltr:pl-3.5 rtl:pr-3.5 text-slate-400 z-10">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <x-web::form.control-group.control
                type="search"
                name="query"
                :value="$filters['query']"
                maxlength="100"
                :placeholder="trans('campusfind_web_web::app.web.browse.search_placeholder')"
                class="h-11 w-full text-sm font-medium ltr:pl-10 rtl:pr-10 [&::-webkit-search-cancel-button]:appearance-none [&::-webkit-search-decoration]:appearance-none"
            />
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-2 shrink-0 justify-end">
            <x-web::button type="submit" variant="primary" size="md" class="h-11 px-5 font-bold">
                @lang('campusfind_web_web::app.web.browse.apply_filters')
            </x-web::button>
            <x-web::button :href="$resetUrl" variant="secondary" size="md" class="h-11 px-4 font-semibold text-slate-600 dark:text-slate-300" data-reset-filters :aria-label="trans('campusfind_web_web::app.web.browse.clear_filters')">
                @lang('campusfind_web_web::app.web.browse.clear_filters')
            </x-web::button>
        </div>
    </div>

    <!-- Official Accordion Component for Secondary Filters -->
    <x-web::accordion :isActive="$hasActiveAccordionFilters" class="border border-slate-200/70 dark:border-slate-800 bg-slate-50/40 dark:bg-slate-800/20 rounded-xl mt-3 overflow-hidden">
        <x-slot:header>
            <div class="flex items-center gap-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 py-0.5">
                <svg class="h-4 w-4 text-[#185c54] dark:text-[#a3e4c8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <span>@lang('campusfind_web_web::app.web.browse.filter_button')</span>

                @if ($activeFiltersCount > 0)
                    <span class="inline-flex items-center justify-center rounded-full bg-[#185c54] px-2 py-0.5 text-[10px] font-extrabold text-white dark:bg-[#a3e4c8] dark:text-[#185c54]">
                        {{ $activeFiltersCount }}
                    </span>
                @endif
            </div>
        </x-slot:header>

        <x-slot:content>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 pt-2 pb-1">
                <x-web::form.control-group name="category" :label="trans('campusfind_web_web::app.web.browse.category_label')">
                    <x-web::form.control-group.control type="select" name="category" :value="$filters['category']">
                        <option value="">@lang('campusfind_web_web::app.web.browse.all_categories')</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->code }}" {{ $filters['category'] === $category->code ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </x-web::form.control-group.control>
                </x-web::form.control-group>

                <x-web::form.control-group name="location" :label="trans('campusfind_web_web::app.web.browse.location_label')">
                    <x-web::form.control-group.control type="search" name="location" :value="$filters['location']" maxlength="100" :placeholder="trans('campusfind_web_web::app.web.browse.location_placeholder')" />
                </x-web::form.control-group>

                <x-web::form.control-group name="status" :label="trans('campusfind_web_web::app.web.browse.status_label')">
                    <x-web::form.control-group.control type="select" name="status" :value="$filters['status'] ?? ''">
                        @foreach (['' => 'all', 'draft' => 'draft', 'active' => 'active', 'reported' => 'reported', 'in_custody' => 'in_custody'] as $value => $label)
                            <option value="{{ $value }}" {{ ($filters['status'] ?? '') === $value ? 'selected' : '' }}>@lang('campusfind_web_web::app.web.browse.status_'.$label)</option>
                        @endforeach
                    </x-web::form.control-group.control>
                </x-web::form.control-group>

                <x-web::form.control-group name="sort" :label="trans('campusfind_web_web::app.web.browse.sort_label')">
                    <x-web::form.control-group.control type="select" name="sort" :value="$filters['sort']">
                        @foreach (['newest', 'oldest', 'title_asc', 'title_desc'] as $sort)
                            <option value="{{ $sort }}" {{ $filters['sort'] === $sort ? 'selected' : '' }}>@lang('campusfind_web_web::app.web.browse.sort_'.$sort)</option>
                        @endforeach
                    </x-web::form.control-group.control>
                </x-web::form.control-group>

                <x-web::form.control-group name="date_from" :label="trans('campusfind_web_web::app.web.browse.date_from')">
                    <x-web::form.control-group.control type="date" name="date_from" :value="$filters['date_from']" />
                </x-web::form.control-group>

                <x-web::form.control-group name="date_to" :label="trans('campusfind_web_web::app.web.browse.date_to')">
                    <x-web::form.control-group.control type="date" name="date_to" :value="$filters['date_to']" />
                </x-web::form.control-group>
            </div>
        </x-slot:content>
    </x-web::accordion>
</x-web::form>
