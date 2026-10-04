@props([
    'tabs' => [],
])

@if (! empty($tabs) && count($tabs) > 0)
    <div {{ $attributes->merge(['class' => 'tabs']) }}>
        <div class="mb-4 flex gap-4 border-b-2 border-slate-200 pt-2 dark:border-slate-800 max-sm:hidden">
            @foreach ($tabs as $tab)
                <a
                    href="{{ $tab['url'] ?? '#' }}"
                    class="{{ (! empty($tab['is_active']) || ! empty($tab['active'])) ? '-mb-px border-b-2 border-[#185c54] text-[#185c54] dark:border-emerald-400 dark:text-emerald-400 font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }} pb-3.5 px-2.5 text-base font-medium transition cursor-pointer text-decoration-none flex items-center gap-2"
                >
                    @if (! empty($tab['icon']))
                        <span class="{{ $tab['icon'] }}"></span>
                    @endif

                    <span>{{ $tab['name'] ?? $tab['title'] ?? '' }}</span>

                    @if (isset($tab['badge']))
                        <x-web::badge :variant="$tab['badge_variant'] ?? 'neutral'" size="sm">
                            {{ $tab['badge'] }}
                        </x-web::badge>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
@endif
