@props([
    'title',
    'description' => null,
])

<x-web::card
    variant="flat"
    padding="xl"
    {{ $attributes->merge(['class' => 'my-8 text-center']) }}
>
    <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-[#e6f4ee] text-[#185c54] shadow-xs ring-4 ring-[#e6f4ee]/60 dark:bg-[#185c54]/30 dark:text-[#a3e4c8] dark:ring-[#185c54]/10" aria-hidden="true">
        @isset($icon)
            {{ $icon }}
        @else
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        @endisset
    </div>

    <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $title }}</h2>

    @if ($description)
        <p class="mx-auto mt-2 max-w-md text-sm leading-relaxed text-slate-500 dark:text-slate-400">{{ $description }}</p>
    @endif

    @if ($slot->isNotEmpty())
        <div class="mt-5">{{ $slot }}</div>
    @endif
</x-web::card>
