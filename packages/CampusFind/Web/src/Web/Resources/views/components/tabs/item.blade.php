@props([
    'title'      => '',
    'isSelected' => false,
])

<v-tab-item
    title="{{ $title }}"
    is-selected="{{ filter_var($isSelected, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false' }}"
    {{ $attributes->merge(['class' => 'py-2']) }}
>
    <template v-slot>
        {{ $slot }}
    </template>
</v-tab-item>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-tab-item-template"
    >
        <div
            v-show="isActive"
            role="tabpanel"
            tabindex="0"
            class="focus:outline-none transition-opacity duration-200"
        >
            <slot></slot>
        </div>
    </script>

    <script type="module">
        app.component('v-tab-item', {
            template: '#v-tab-item-template',

            props: {
                title: String,
                isSelected: {
                    type: [Boolean, String],
                    default: false,
                },
            },

            data() {
                return {
                    isActive: false,
                };
            },

            mounted() {
                this.isActive = String(this.isSelected) === 'true' || this.isSelected === true;

                if (this.$parent && this.$parent.tabs) {
                    this.$parent.tabs.push(this);
                }
            },
        });
    </script>
@endPushOnce
