@props([
    'type' => 'text',
    'name' => '',
])

@php
    $hasError = $name && isset($errors) && $errors->has($name);
    $controlId = $attributes->get('id', $name);
    $errorId = $name ? "{$name}-error" : null;
@endphp

@switch($type)
    @case('hidden')
        <v-field
            v-slot="{ field, errors }"
            {{ $attributes->only(['name', ':name', 'value', ':value', 'v-model', 'rules', ':rules', 'label', ':label']) }}
            name="{{ $name }}"
        >
            <input
                type="hidden"
                name="{{ $name }}"
                id="{{ $controlId }}"
                v-bind="field"
                {{ $attributes->except(['value', ':value', 'v-model', 'rules', ':rules', 'label', ':label', 'id']) }}
            />
        </v-field>

        @break

    @case('text')
    @case('email')
    @case('password')
    @case('number')
    @case('time')
    @case('datetime-local')
    @case('search')
        <v-field
            v-slot="{ field, errors }"
            {{ $attributes->only(['name', ':name', 'value', ':value', 'v-model', 'rules', ':rules', 'label', ':label']) }}
            name="{{ $name }}"
        >
            <input
                type="{{ $type }}"
                name="{{ $name }}"
                id="{{ $controlId }}"
                v-bind="field"
                @if ($hasError)
                    aria-invalid="true"
                    aria-describedby="{{ $errorId }}"
                @endif
                :class="[errors.length ? 'border !border-rose-500 hover:border-rose-500 focus:border-rose-500 focus:ring-rose-500/15' : '']"
                {{ $attributes->except(['value', ':value', 'v-model', 'rules', ':rules', 'label', ':label', 'id'])->merge(['class' => 'w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 px-3.5 py-2.5 text-sm font-medium text-slate-900 dark:text-white placeholder-slate-400 transition-all hover:border-slate-400 focus:border-[#185c54] focus:outline-none focus:ring-4 focus:ring-[#185c54]/15']) }}
            />
        </v-field>

        @break

    @case('price')
        <v-field
            v-slot="{ field, errors }"
            {{ $attributes->only(['name', ':name', 'value', ':value', 'v-model', 'rules', ':rules', 'label', ':label']) }}
            name="{{ $name }}"
        >
            <div
                class="flex w-full items-center overflow-hidden rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-sm text-slate-800 transition-all focus-within:border-[#185c54] focus-within:ring-4 focus-within:ring-[#185c54]/15 hover:border-slate-400 dark:text-white"
                :class="[errors.length ? 'border !border-rose-500 hover:border-rose-500' : '']"
            >
                @if (isset($currency))
                    <span {{ $currency->attributes->merge(['class' => 'py-2.5 text-slate-500 ltr:pl-4 rtl:pr-4 font-bold']) }}>
                        {{ $currency }}
                    </span>
                @else
                    <span class="py-2.5 text-slate-500 ltr:pl-4 rtl:pr-4 font-bold">
                        {{ config('app.currency', 'SAR') }}
                    </span>
                @endif

                <input
                    type="text"
                    name="{{ $name }}"
                    id="{{ $controlId }}"
                    v-bind="field"
                    @if ($hasError)
                        aria-invalid="true"
                        aria-describedby="{{ $errorId }}"
                    @endif
                    {{ $attributes->except(['value', ':value', 'v-model', 'rules', ':rules', 'label', ':label', 'id'])->merge(['class' => 'w-full p-2.5 text-sm text-slate-800 dark:bg-slate-800 dark:text-white border-0 outline-none']) }}
                />
            </div>
        </v-field>

        @break

    @case('file')
        <v-field
            v-slot="{ field, errors, handleChange, handleBlur }"
            {{ $attributes->only(['name', ':name', 'value', ':value', 'v-model', 'rules', ':rules', 'label', ':label']) }}
            name="{{ $name }}"
        >
            <input
                type="{{ $type }}"
                id="{{ $controlId }}"
                v-bind="{ name: field.name }"
                @if ($hasError)
                    aria-invalid="true"
                    aria-describedby="{{ $errorId }}"
                @endif
                :class="[errors.length ? 'border !border-rose-500 hover:border-rose-500' : '']"
                {{ $attributes->except(['value', ':value', 'v-model', 'rules', ':rules', 'label', ':label', 'id'])->merge(['class' => 'w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 px-3.5 py-2 text-sm font-medium text-slate-800 dark:text-white transition-all file:me-3 file:rounded-lg file:border-0 file:bg-[#185c54] file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-white hover:file:bg-[#134942] dark:file:bg-[#185c54] cursor-pointer focus:outline-none focus:ring-4 focus:ring-[#185c54]/15']) }}
                @change="handleChange"
                @blur="handleBlur"
            />
        </v-field>

        @break

    @case('color')
        <v-field
            name="{{ $name }}"
            v-slot="{ field, errors }"
            {{ $attributes->except('class') }}
        >
            <input
                type="{{ $type }}"
                id="{{ $controlId }}"
                :class="[errors.length ? 'border border-rose-500' : '']"
                v-bind="field"
                @if ($hasError)
                    aria-invalid="true"
                    aria-describedby="{{ $errorId }}"
                @endif
                {{ $attributes->except(['value', 'id'])->merge(['class' => 'w-full appearance-none rounded-xl border border-slate-300 dark:border-slate-700 h-10 transition-all hover:border-slate-400']) }}
            >
        </v-field>
        @break

    @case('textarea')
        <v-field
            v-slot="{ field, errors }"
            {{ $attributes->only(['name', ':name', 'value', ':value', 'v-model', 'rules', ':rules', 'label', ':label']) }}
            name="{{ $name }}"
        >
            <textarea
                name="{{ $name }}"
                id="{{ $controlId }}"
                v-bind="field"
                @if ($hasError)
                    aria-invalid="true"
                    aria-describedby="{{ $errorId }}"
                @endif
                :class="[errors.length ? 'border !border-rose-500 hover:border-rose-500 focus:border-rose-500 focus:ring-rose-500/15' : '']"
                {{ $attributes->except(['value', ':value', 'v-model', 'rules', ':rules', 'label', ':label', 'id'])->merge(['class' => 'w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 px-3.5 py-2.5 text-sm font-medium text-slate-900 dark:text-white placeholder-slate-400 transition-all hover:border-slate-400 focus:border-[#185c54] focus:outline-none focus:ring-4 focus:ring-[#185c54]/15']) }}
            >{{ $slot }}</textarea>
        </v-field>

        @break

    @case('date')
    @case('flatpickr-date')
    @case('date-picker')
        <v-field
            v-slot="{ field, errors }"
            {{ $attributes->only(['name', ':name', 'value', ':value', 'v-model', 'rules', ':rules', 'label', ':label'])->merge(['rules' => 'regex:^\d{4}-\d{2}-\d{2}$']) }}
            name="{{ $name }}"
        >
            <x-web::flat-picker.date>
                <input
                    name="{{ $name }}"
                    id="{{ $controlId }}"
                    v-bind="field"
                    @if ($hasError)
                        aria-invalid="true"
                        aria-describedby="{{ $errorId }}"
                    @endif
                    :class="[errors.length ? 'border !border-rose-500 hover:border-rose-500' : '']"
                    {{ $attributes->except(['value', ':value', 'v-model', 'rules', ':rules', 'label', ':label', 'id'])->merge(['class' => 'w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 px-3.5 py-2.5 text-sm font-medium text-slate-900 dark:text-white transition-all hover:border-slate-400 focus:border-[#185c54] focus:outline-none focus:ring-4 focus:ring-[#185c54]/15']) }}
                    autocomplete="off"
                />
            </x-web::flat-picker.date>
        </v-field>

        @break

    @case('datetime')
    @case('flatpickr-datetime')
    @case('datetime-picker')
        <v-field
            v-slot="{ field, errors }"
            {{ $attributes->only(['name', ':name', 'value', ':value', 'v-model', 'rules', ':rules', 'label', ':label'])->merge(['rules' => 'regex:^\d{4}-\d{2}-\d{2} \d{2}:\d{2}(:\d{2})?$']) }}
            name="{{ $name }}"
        >
            <x-web::flat-picker.datetime>
                <input
                    name="{{ $name }}"
                    id="{{ $controlId }}"
                    v-bind="field"
                    @if ($hasError)
                        aria-invalid="true"
                        aria-describedby="{{ $errorId }}"
                    @endif
                    :class="[errors.length ? 'border !border-rose-500 hover:border-rose-500' : '']"
                    {{ $attributes->except(['value', ':value', 'v-model', 'rules', ':rules', 'label', ':label', 'id'])->merge(['class' => 'w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 px-3.5 py-2.5 text-sm font-medium text-slate-900 dark:text-white transition-all hover:border-slate-400 focus:border-[#185c54] focus:outline-none focus:ring-4 focus:ring-[#185c54]/15']) }}
                    autocomplete="off"
                />
            </x-web::flat-picker.datetime>
        </v-field>

        @break

    @case('select')
        <v-field
            v-slot="{ field, errors }"
            {{ $attributes->only(['name', ':name', 'value', ':value', 'v-model', 'rules', ':rules', 'label', ':label']) }}
            name="{{ $name }}"
        >
            <div class="relative w-full">
                <select
                    name="{{ $name }}"
                    id="{{ $controlId }}"
                    v-bind="field"
                    @if ($hasError)
                        aria-invalid="true"
                        aria-describedby="{{ $errorId }}"
                    @endif
                    :class="[errors.length ? 'border !border-rose-500 hover:border-rose-500 focus:border-rose-500 focus:ring-rose-500/15' : '']"
                    {{ $attributes->except(['value', ':value', 'v-model', 'rules', ':rules', 'label', ':label', 'id'])->merge(['class' => 'w-full appearance-none rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 px-3.5 py-2.5 ltr:pr-10 rtl:pl-10 text-sm font-medium text-slate-900 dark:text-white transition-all hover:border-slate-400 focus:border-[#185c54] focus:outline-none focus:ring-4 focus:ring-[#185c54]/15 cursor-pointer']) }}
                >
                    {{ $slot }}
                </select>

                <div class="pointer-events-none absolute inset-y-0 ltr:right-0 rtl:left-0 flex items-center ltr:pr-3.5 rtl:pl-3.5 text-slate-400 dark:text-slate-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>
        </v-field>

        @break

    @case('multiselect')
        <v-field
            as="select"
            v-slot="{ value }"
            :class="[errors && errors['{{ $name }}'] ? 'border !border-rose-500 hover:border-rose-500' : '']"
            @if ($hasError)
                aria-invalid="true"
                aria-describedby="{{ $errorId }}"
            @endif
            {{ $attributes->except(['id'])->merge(['class' => 'flex w-full flex-col rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 px-3.5 py-2.5 text-sm font-medium text-slate-900 dark:text-white transition-all hover:border-slate-400 focus:border-[#185c54] focus:outline-none focus:ring-4 focus:ring-[#185c54]/15']) }}
            name="{{ $name }}"
            id="{{ $controlId }}"
            multiple
        >
            {{ $slot }}
        </v-field>

        @break

    @case('checkbox')
        <div class="flex items-center gap-2">
            <v-field
                v-slot="{ field }"
                type="checkbox"
                class="hidden"
                {{ $attributes->only(['name', ':name', 'value', ':value', 'v-model', 'rules', ':rules', 'label', ':label', 'key', ':key']) }}
                name="{{ $name }}"
            >
                <input
                    type="checkbox"
                    name="{{ $name }}"
                    v-bind="field"
                    id="{{ $controlId }}"
                    value="{{ $attributes->get('value', '1') }}"
                    class="h-4 w-4 rounded border-slate-300 dark:border-slate-700 text-[#185c54] focus:ring-[#185c54] cursor-pointer"
                    @if ($hasError)
                        aria-invalid="true"
                        aria-describedby="{{ $errorId }}"
                    @endif
                    {{ $attributes->except(['rules', 'label', ':label', 'key', ':key', 'id']) }}
                />

                <v-checked-handler
                    :field="field"
                    checked="{{ $attributes->get('checked') }}"
                >
                </v-checked-handler>
            </v-field>

            @if ($slot->isNotEmpty())
                <label
                    for="{{ $controlId }}"
                    {{
                        $attributes
                            ->only(['class'])
                            ->merge(['class' => 'text-sm font-medium text-slate-700 dark:text-slate-300 select-none'])
                            ->merge(['class' => $attributes->get('disabled') ? 'cursor-not-allowed opacity-70' : 'cursor-pointer'])
                    }}
                >
                    {{ $slot }}
                </label>
            @endif
        </div>

        @break

    @case('radio')
        <div class="flex items-center gap-2">
            <v-field
                type="radio"
                class="hidden"
                v-slot="{ field }"
                {{ $attributes->only(['name', ':name', 'value', ':value', 'v-model', 'rules', ':rules', 'label', ':label', 'key', ':key']) }}
                name="{{ $name }}"
            >
                <input
                    type="radio"
                    name="{{ $name }}"
                    v-bind="field"
                    id="{{ $attributes->get('id', $name . '_' . $attributes->get('value')) }}"
                    value="{{ $attributes->get('value') }}"
                    class="h-4 w-4 border-slate-300 dark:border-slate-700 text-[#185c54] focus:ring-[#185c54] cursor-pointer"
                    @if ($hasError)
                        aria-invalid="true"
                        aria-describedby="{{ $errorId }}"
                    @endif
                    {{ $attributes->except(['rules', 'label', ':label', 'key', ':key', 'id']) }}
                />

                <v-checked-handler
                    class="hidden"
                    :field="field"
                    checked="{{ $attributes->get('checked') }}"
                >
                </v-checked-handler>
            </v-field>

            @if ($slot->isNotEmpty())
                <label
                    for="{{ $attributes->get('id', $name . '_' . $attributes->get('value')) }}"
                    class="text-sm font-medium text-slate-700 dark:text-slate-300 cursor-pointer select-none"
                >
                    {{ $slot }}
                </label>
            @endif
        </div>

        @break

    @case('switch')
        <label class="relative inline-flex cursor-pointer items-center select-none">
            <v-field
                type="checkbox"
                class="hidden"
                v-slot="{ field }"
                {{ $attributes->only(['name', ':name', 'value', ':value', 'v-model', 'rules', ':rules', 'label', ':label', 'key', ':key']) }}
                name="{{ $name }}"
            >
                <input
                    type="checkbox"
                    name="{{ $name }}"
                    id="{{ $controlId }}"
                    class="peer sr-only"
                    v-bind="field"
                    value="{{ $attributes->get('value', '1') }}"
                    @if ($hasError)
                        aria-invalid="true"
                        aria-describedby="{{ $errorId }}"
                    @endif
                    {{ $attributes->except(['v-model', 'rules', ':rules', 'label', ':label', 'key', ':key', 'id']) }}
                />

                <v-checked-handler
                    class="hidden"
                    :field="field"
                    checked="{{ $attributes->get('checked') }}"
                >
                </v-checked-handler>
            </v-field>

            <label
                class="peer h-6 w-11 cursor-pointer rounded-full bg-slate-200 after:absolute after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-slate-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-[#185c54] peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#185c54]/20 dark:bg-slate-700 dark:after:border-white dark:after:bg-white after:ltr:left-[2px] peer-checked:after:ltr:translate-x-full after:rtl:right-[2px] peer-checked:after:rtl:-translate-x-full"
                for="{{ $controlId }}"
            ></label>

            @if ($slot->isNotEmpty())
                <span class="ltr:ml-3 rtl:mr-3 text-sm font-medium text-slate-700 dark:text-slate-300">{{ $slot }}</span>
            @endif
        </label>

        @break

    @case('image')
        <x-web::media.images
            name="{{ $name }}"
            ::class="[errors && errors['{{ $name }}'] ? 'border !border-rose-500 hover:border-rose-500' : '']"
            {{ $attributes }}
        />

        @break

    @case('custom')
        <v-field {{ $attributes }}>
            {{ $slot }}
        </v-field>

        @break

    @case('tags')
        <x-web::tags
            name="{{ $name }}"
            :value="$attributes->get(':data') ?? $attributes->get('data') ?? []"
            {{ $attributes }}
        />
        @break

    @default
        <v-field
            v-slot="{ field, errors }"
            {{ $attributes->only(['name', ':name', 'value', ':value', 'v-model', 'rules', ':rules', 'label', ':label']) }}
            name="{{ $name }}"
        >
            <input
                type="{{ $type }}"
                name="{{ $name }}"
                id="{{ $controlId }}"
                v-bind="field"
                @if ($hasError)
                    aria-invalid="true"
                    aria-describedby="{{ $errorId }}"
                @endif
                :class="[errors.length ? 'border !border-rose-500 hover:border-rose-500 focus:border-rose-500 focus:ring-rose-500/15' : '']"
                {{ $attributes->except(['value', ':value', 'v-model', 'rules', ':rules', 'label', ':label', 'id'])->merge(['class' => 'w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 px-3.5 py-2.5 text-sm font-medium text-slate-900 dark:text-white placeholder-slate-400 transition-all hover:border-slate-400 focus:border-[#185c54] focus:outline-none focus:ring-4 focus:ring-[#185c54]/15']) }}
            />
        </v-field>
@endswitch

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-checked-handler-template"
    >
    </script>

    <script type="module">
        app.component('v-checked-handler', {
            template: '#v-checked-handler-template',

            props: ['field', 'checked'],

            mounted() {
                if (this.checked == '' || this.checked === false || this.checked === 'false' || this.checked === undefined) {
                    return;
                }

                this.field.checked = true;

                if (typeof this.field.onChange === 'function') {
                    this.field.onChange();
                }
            },
        });
    </script>
@endpushOnce
