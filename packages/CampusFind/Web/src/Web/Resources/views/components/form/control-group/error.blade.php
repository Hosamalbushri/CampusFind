@props([
    'name'        => null,
    'controlName' => '',
])

@php
    $errorKey = $name ?? $controlName;
    $errorId = $attributes->get('id', $errorKey ? "{$errorKey}-error" : null);
@endphp

<v-error-message
    name="{{ $errorKey }}"
    v-slot="{ message }"
>
    <p
        @if ($errorId) id="{{ $errorId }}" @endif
        role="alert"
        aria-live="polite"
        {{ $attributes->except('id')->merge(['class' => 'mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400']) }}
        v-text="message"
    >
        @if ($errorKey && isset($errors) && $errors->has($errorKey))
            {{ $errors->first($errorKey) }}
        @endif
    </p>
</v-error-message>
