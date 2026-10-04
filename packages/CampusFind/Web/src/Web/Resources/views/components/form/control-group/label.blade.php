@props([
    'required' => false,
])

<label {{ $attributes->merge(['class' => 'mb-1.5 flex items-center gap-1 text-sm font-semibold text-slate-700 dark:text-slate-200 select-none']) }}>
    {{ $slot }}

    @if ($required)
        <span class="text-rose-500 font-bold" aria-hidden="true">*</span>
    @endif
</label>
