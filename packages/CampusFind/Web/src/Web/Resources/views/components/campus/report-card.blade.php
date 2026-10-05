@props(['record'])

@php
    $type = $record->type ?? 'found';
    $typeVariant = match ($type) {
        'found' => 'success',
        'lost'  => 'warning',
        default => 'mint',
    };
    $publicId = $record->publicId ?? ('FI-' . ($record->reference ?? ''));
    $location = $record->location ?? $record->foundLocation ?? null;
    $occurredAt = $record->occurredAt ?? $record->foundAt ?? null;
    $actionAvailable = $record->actionAvailable ?? true;
    $detailDestination = $record->detailDestination ?? 'found_item';
@endphp

<x-web::card
    variant="interactive"
    padding="none"
    {{ $attributes->merge(['class' => 'group flex flex-col overflow-hidden transition-all duration-300']) }}
    data-public-id="{{ $publicId }}"
    data-lazy-item
>
    <div class="relative flex h-52 items-center justify-center overflow-hidden bg-gradient-to-br from-slate-100 via-slate-50 to-[#e6f4ee]/50 dark:from-slate-800 dark:via-slate-850 dark:to-[#185c54]/20">
        @if ($record->hasImage && $record->imageUrl)
            <img src="{{ $record->imageUrl }}" alt="{{ $record->title }}" loading="lazy" decoding="async" class="h-full w-full object-cover transition-transform duration-300 motion-reduce:transition-none motion-reduce:transform-none group-hover:scale-105">
        @else
            <div class="flex flex-col items-center justify-center p-4 text-center select-none" aria-hidden="true">
                <div class="h-14 w-14 rounded-2xl bg-white/90 dark:bg-slate-800/90 shadow-2xs border border-slate-200/80 dark:border-slate-700/80 flex items-center justify-center text-slate-400 dark:text-slate-500 transition-transform duration-300 group-hover:scale-110">
                    <svg class="h-7 w-7 stroke-[1.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <span class="mt-2.5 text-[11px] font-bold text-slate-400 dark:text-slate-500">@lang('campusfind_web_web::app.web.browse.no_public_image')</span>
            </div>
        @endif

        <div class="absolute inset-x-3 top-3 flex items-start justify-between gap-2 z-10">
            <x-web::badge :variant="$typeVariant" size="xs" :dot="true" class="shadow-xs">
                @lang('campusfind_web_web::app.web.browse.record_'.$type)
            </x-web::badge>

            @if ($record->category)
                <x-web::badge variant="outline" size="xs" class="bg-white/95 text-slate-900 dark:bg-slate-900 dark:text-slate-100 font-bold border border-slate-300 dark:border-slate-700 shadow-xs max-w-[65%] shrink-0">
                    {{ $record->category }}
                </x-web::badge>
            @endif
        </div>
    </div>

    <div class="flex flex-1 flex-col justify-between p-6">
        <div>
            <h2 class="line-clamp-1 text-lg font-bold text-slate-900 transition-colors group-hover:text-[#185c54] dark:text-white dark:group-hover:text-[#a3e4c8]">
                {{ $record->title }}
            </h2>

            @if ($record->description)
                <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-slate-500 dark:text-slate-400">
                    {{ $record->description }}
                </p>
            @endif

            <dl class="mt-5 space-y-2.5 text-xs font-medium text-slate-500 dark:text-slate-400">
                @if ($location)
                    <div class="flex items-center gap-2">
                        <svg class="h-4 w-4 shrink-0 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <dt class="font-semibold text-slate-700 dark:text-slate-300">@lang('campusfind_web_web::app.web.browse.location_'.$type):</dt>
                        <dd class="truncate text-slate-600 dark:text-slate-300">{{ $location }}</dd>
                    </div>
                @endif

                @if ($occurredAt)
                    <div class="flex items-center gap-2">
                        <svg class="h-4 w-4 shrink-0 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <dt class="font-semibold text-slate-700 dark:text-slate-300">@lang('campusfind_web_web::app.web.browse.date_'.$type):</dt>
                        <dd class="text-slate-600 dark:text-slate-300">
                            <time datetime="{{ $occurredAt->format('Y-m-d') }}">{{ $occurredAt->format('Y-m-d') }}</time>
                        </dd>
                    </div>
                @endif
            </dl>
        </div>

        @if ($actionAvailable)
            <div class="mt-6 border-t border-slate-100 pt-4 dark:border-slate-800/80">
                @if ($type === 'found' && $detailDestination === 'found_item')
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <x-web::button href="{{ route('campusfind_web.web.items.show', ['reference' => $record->reference]) }}" variant="primary" size="sm">
                            @lang('campusfind_web_web::app.web.browse.claim_ownership')
                        </x-web::button>
                        <x-web::button href="{{ route('campusfind_web.web.items.show', ['reference' => $record->reference]) }}" variant="ghost" size="sm" class="text-[#185c54] dark:text-[#a3e4c8] inline-flex items-center gap-1">
                            <span>@lang('campusfind_web_web::app.web.browse.view_details')</span>
                            <svg class="h-3.5 w-3.5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                        </x-web::button>
                    </div>
                @elseif ($type === 'lost' && $detailDestination === 'lost_report')
                    <x-web::button href="{{ route('campusfind_web.web.lost-reports.show', ['reference' => $record->reference]) }}" variant="primary" size="sm" class="w-full justify-center">
                        @lang('campusfind_web_web::app.web.browse.i_found_this_item')
                    </x-web::button>
                @endif
            </div>
        @endif
    </div>
</x-web::card>
