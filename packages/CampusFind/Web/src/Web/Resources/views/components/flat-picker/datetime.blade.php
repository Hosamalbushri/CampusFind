@props([
    'name'        => '',
    'value'       => null,
    'placeholder' => null,
])

<v-datetime-picker {{ $attributes }}>
    @if ($slot->isNotEmpty())
        {{ $slot }}
    @else
        <div class="relative w-full">
            <input
                type="datetime-local"
                name="{{ $name }}"
                value="{{ old($name, $value) }}"
                placeholder="{{ $placeholder }}"
                class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 px-4 py-3 text-sm text-slate-900 dark:text-white placeholder-slate-400 transition-all focus:outline-none focus:border-[#185c54] focus:ring-4 focus:ring-[#185c54]/15"
            />
            <div class="pointer-events-none absolute inset-y-0 ltr:right-3.5 rtl:left-3.5 flex items-center text-slate-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    @endif
</v-datetime-picker>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-datetime-picker-template"
    >
        <span class="relative inline-block w-full font-cairo">
            <slot></slot>
        </span>
    </script>

    <script type="module">
        app.component('v-datetime-picker', {
            template: '#v-datetime-picker-template',

            props: {
                name: String,
                value: String,
                allowInput: {
                    type: Boolean,
                    default: true,
                },
                disable: Array,
                minDate: String,
                maxDate: String,
            },

            emits: ['onChange'],

            data() {
                return {
                    datepicker: null,
                };
            },

            mounted() {
                const options = this.setOptions();
                this.activate(options);
            },

            methods: {
                setOptions() {
                    const self = this;

                    return {
                        allowInput: this.allowInput ?? true,
                        disable: this.disable ?? [],
                        minDate: this.minDate ?? '',
                        maxDate: this.maxDate ?? '',
                        altFormat: "Y-m-d H:i:S",
                        dateFormat: "Y-m-d H:i:S",
                        enableTime: true,
                        time_24hr: true,
                        weekNumbers: true,

                        onChange(selectedDates, dateStr, instance) {
                            self.$emit("onChange", dateStr);
                        },
                    };
                },

                activate(options) {
                    const element = this.$el.getElementsByTagName("input")[0];

                    if (element && typeof window.Flatpickr === 'function') {
                        this.datepicker = new window.Flatpickr(element, options);
                    }
                },

                clear() {
                    if (this.datepicker) {
                        this.datepicker.clear();
                    }
                },
            },
        });
    </script>
@endPushOnce
