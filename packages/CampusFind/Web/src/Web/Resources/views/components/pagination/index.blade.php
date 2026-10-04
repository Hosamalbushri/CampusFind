@props([
    'current',
    'last',
    'previousUrl' => null,
    'nextUrl'     => null,
    'label',
    'pageLabel',
    'previousLabel',
    'nextLabel',
])

<nav {{ $attributes->merge(['class' => 'mt-12 flex items-center justify-center gap-3']) }} aria-label="{{ $label }}">
    @if ($previousUrl)
        <x-web::button :href="$previousUrl" variant="secondary" size="sm" rel="prev">
            <span aria-hidden="true" class="rtl:rotate-180">&larr;</span> {{ $previousLabel }}
        </x-web::button>
    @endif

    <span class="rounded-xl border border-slate-200/60 bg-slate-100 px-4 py-2 text-xs font-bold text-slate-600 dark:border-slate-700/60 dark:bg-slate-800 dark:text-slate-300 sm:text-sm" aria-current="page">
        {{ $pageLabel }}
    </span>

    @if ($nextUrl)
        <x-web::button :href="$nextUrl" variant="secondary" size="sm" rel="next">
            {{ $nextLabel }} <span aria-hidden="true" class="rtl:rotate-180">&rarr;</span>
        </x-web::button>
    @endif
</nav>
