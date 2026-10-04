@props([
    'filters',
    'categories',
    'action',
    'resetUrl',
])

<x-web::form
    method="GET"
    :action="$action"
    {{ $attributes->merge(['class' => 'space-y-5 rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-6']) }}
    data-unified-search-form
>
    @if (request()->filled('locale'))
        <x-web::form.control-group.control type="hidden" name="locale" :value="request()->query('locale')" />
    @endif

    <fieldset>
        <legend class="mb-2 text-sm font-bold text-slate-800 dark:text-slate-200">@lang('campusfind_web_web::app.web.browse.type_label')</legend>
        <div class="flex flex-wrap gap-2">
            @foreach (['all', 'lost', 'found'] as $type)
                <label class="cursor-pointer">
                    <input type="radio" name="type" value="{{ $type }}" class="peer sr-only" @checked($filters['type'] === $type)>
                    <span class="inline-flex rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-600 transition-colors peer-checked:border-[#185c54] peer-checked:bg-[#e6f4ee] peer-checked:text-[#185c54] peer-focus-visible:ring-4 peer-focus-visible:ring-[#185c54]/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:peer-checked:border-[#a3e4c8] dark:peer-checked:bg-[#185c54]/30 dark:peer-checked:text-[#a3e4c8]">
                        @lang('campusfind_web_web::app.web.browse.type_'.$type)
                    </span>
                </label>
            @endforeach
        </div>
    </fieldset>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-web::form.control-group name="query" :label="trans('campusfind_web_web::app.web.browse.search_label')" class="md:col-span-2">
            <x-web::form.control-group.control type="search" name="query" :value="$filters['query']" maxlength="100" :placeholder="trans('campusfind_web_web::app.web.browse.search_placeholder')" />
        </x-web::form.control-group>

        <x-web::form.control-group name="category" :label="trans('campusfind_web_web::app.web.browse.category_label')">
            <x-web::form.control-group.control type="select" name="category" :value="$filters['category']">
                <option value="">@lang('campusfind_web_web::app.web.browse.all_categories')</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->code }}" @selected($filters['category'] === $category->code)>{{ $category->name }}</option>
                @endforeach
            </x-web::form.control-group.control>
        </x-web::form.control-group>

        <x-web::form.control-group name="location" :label="trans('campusfind_web_web::app.web.browse.location_label')">
            <x-web::form.control-group.control type="search" name="location" :value="$filters['location']" maxlength="100" :placeholder="trans('campusfind_web_web::app.web.browse.location_placeholder')" />
        </x-web::form.control-group>

        <x-web::form.control-group name="date_from" :label="trans('campusfind_web_web::app.web.browse.date_from')">
            <x-web::form.control-group.control type="date" name="date_from" :value="$filters['date_from']" />
        </x-web::form.control-group>

        <x-web::form.control-group name="date_to" :label="trans('campusfind_web_web::app.web.browse.date_to')">
            <x-web::form.control-group.control type="date" name="date_to" :value="$filters['date_to']" />
        </x-web::form.control-group>

        <x-web::form.control-group name="status" :label="trans('campusfind_web_web::app.web.browse.status_label')">
            <x-web::form.control-group.control type="select" name="status" :value="$filters['status'] ?? ''">
                @foreach (['' => 'all', 'active' => 'active', 'reported' => 'reported', 'in_custody' => 'in_custody'] as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>@lang('campusfind_web_web::app.web.browse.status_'.$label)</option>
                @endforeach
            </x-web::form.control-group.control>
        </x-web::form.control-group>

        <x-web::form.control-group name="sort" :label="trans('campusfind_web_web::app.web.browse.sort_label')">
            <x-web::form.control-group.control type="select" name="sort" :value="$filters['sort']">
                @foreach (['newest', 'oldest', 'title_asc', 'title_desc'] as $sort)
                    <option value="{{ $sort }}" @selected($filters['sort'] === $sort)>@lang('campusfind_web_web::app.web.browse.sort_'.$sort)</option>
                @endforeach
            </x-web::form.control-group.control>
        </x-web::form.control-group>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <x-web::button type="submit" variant="primary" size="lg">@lang('campusfind_web_web::app.web.browse.apply_filters')</x-web::button>
        <x-web::button :href="$resetUrl" variant="secondary" size="lg" :aria-label="trans('campusfind_web_web::app.web.browse.clear_filters')">@lang('campusfind_web_web::app.web.browse.clear_filters')</x-web::button>
    </div>
</x-web::form>
