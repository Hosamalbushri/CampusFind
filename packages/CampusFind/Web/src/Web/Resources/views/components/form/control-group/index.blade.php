@props([
    'name'     => null,
    'label'    => null,
    'required' => false,
    'hint'     => null,
])

<div {{ $attributes->merge(['class' => 'mb-4']) }}>
    @if ($label)
        <x-web::form.control-group.label :for="$name" :required="$required">
            {{ $label }}
        </x-web::form.control-group.label>
    @endif

    {{ $slot }}

    @if ($hint)
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $hint }}</p>
    @endif

    @if ($name && ! $slot->isEmpty() && ! str_contains($slot, 'v-error-message') && ! str_contains($slot, 'form.control-group.error'))
        <x-web::form.control-group.error :name="$name" />
    @endif
</div>
