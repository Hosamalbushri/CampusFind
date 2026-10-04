@props([
    'position' => 'bottom-left',
])

<v-dropdown
    position="{{ $position }}"
    {{ $attributes->merge(['class' => 'relative inline-block text-start']) }}
>
    @isset($toggle)
        <template v-slot:toggle>
            {{ $toggle }}
        </template>
    @endisset

    @isset($content)
        <template #content="{ isActive, positionStyles }">
            <div
                {{ $content->attributes->merge(['class' => 'absolute z-50 min-w-[200px] rounded-2xl bg-white dark:bg-slate-900 p-2 shadow-xl border border-slate-200/80 dark:border-slate-800 transition-all font-cairo']) }}
                :style="positionStyles"
                v-show="isActive"
                role="menu"
            >
                {{ $content }}
            </div>
        </template>
    @endisset

    @isset($menu)
        <template #menu="{ isActive, positionStyles }">
            <ul
                {{ $menu->attributes->merge(['class' => 'absolute z-50 min-w-[200px] rounded-2xl bg-white dark:bg-slate-900 p-2 shadow-xl border border-slate-200/80 dark:border-slate-800 list-none transition-all font-cairo m-0']) }}
                :style="positionStyles"
                v-show="isActive"
                role="menu"
            >
                {{ $menu }}
            </ul>
        </template>
    @endisset
</v-dropdown>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-dropdown-template"
    >
        <div class="relative inline-block text-start font-cairo">
            <div
                class="cursor-pointer select-none"
                ref="toggleBlock"
                @click="toggle()"
                role="button"
                tabindex="0"
                :aria-expanded="isActive ? 'true' : 'false'"
                aria-haspopup="menu"
                @keydown.enter.prevent="toggle()"
                @keydown.space.prevent="toggle()"
            >
                <slot name="toggle"></slot>
            </div>

            <transition
                tag="div"
                name="dropdown"
                enter-active-class="transition duration-100 ease-out"
                enter-from-class="scale-95 transform opacity-0"
                enter-to-class="scale-100 transform opacity-100"
                leave-active-class="transition duration-75 ease-in"
                leave-from-class="scale-100 transform opacity-100"
                leave-to-class="scale-95 transform opacity-0"
            >
                <div>
                    <slot
                        name="content"
                        :position-styles="positionStyles"
                        :is-active="isActive"
                        :isActive="isActive"
                    ></slot>

                    <slot
                        name="menu"
                        :position-styles="positionStyles"
                        :is-active="isActive"
                        :isActive="isActive"
                    ></slot>
                </div>
            </transition>
        </div>
    </script>

    <script type="module">
        app.component('v-dropdown', {
            template: '#v-dropdown-template',

            props: {
                position: {
                    type: String,
                    default: 'bottom-left',
                },

                closeOnClick: {
                    type: Boolean,
                    default: true,
                },
            },

            data() {
                return {
                    toggleBlockWidth: 0,
                    toggleBlockHeight: 0,
                    isActive: false,
                };
            },

            created() {
                window.addEventListener('click', this.handleFocusOut);
                window.addEventListener('keydown', this.handleKeyDown);
            },

            mounted() {
                if (this.$refs.toggleBlock) {
                    this.toggleBlockWidth = this.$refs.toggleBlock.clientWidth;
                    this.toggleBlockHeight = this.$refs.toggleBlock.clientHeight;
                }
            },

            beforeUnmount() {
                window.removeEventListener('click', this.handleFocusOut);
                window.removeEventListener('keydown', this.handleKeyDown);
            },

            computed: {
                positionStyles() {
                    const isRtl = document.documentElement.getAttribute('dir') === 'rtl';

                    switch (this.position) {
                        case 'bottom-left':
                            return isRtl ? [
                                `top: ${this.toggleBlockHeight + 8}px`,
                                'right: 0',
                            ] : [
                                `top: ${this.toggleBlockHeight + 8}px`,
                                'left: 0',
                            ];

                        case 'bottom-right':
                            return isRtl ? [
                                `top: ${this.toggleBlockHeight + 8}px`,
                                'left: 0',
                            ] : [
                                `top: ${this.toggleBlockHeight + 8}px`,
                                'right: 0',
                            ];

                        case 'top-left':
                            return isRtl ? [
                                `bottom: ${this.toggleBlockHeight + 8}px`,
                                'right: 0',
                            ] : [
                                `bottom: ${this.toggleBlockHeight + 8}px`,
                                'left: 0',
                            ];

                        case 'top-right':
                            return isRtl ? [
                                `bottom: ${this.toggleBlockHeight + 8}px`,
                                'left: 0',
                            ] : [
                                `bottom: ${this.toggleBlockHeight + 8}px`,
                                'right: 0',
                            ];

                        default:
                            return [
                                `top: ${this.toggleBlockHeight + 8}px`,
                                isRtl ? 'right: 0' : 'left: 0',
                            ];
                    }
                },
            },

            methods: {
                toggle() {
                    this.isActive = ! this.isActive;
                },

                handleKeyDown(e) {
                    if (e.key === 'Escape' && this.isActive) {
                        this.isActive = false;
                    }
                },

                handleFocusOut(e) {
                    if (! this.$el.contains(e.target) || (this.closeOnClick && this.$el.children[1] && this.$el.children[1].contains(e.target))) {
                        this.isActive = false;
                    }
                },
            },
        });
    </script>
@endPushOnce
