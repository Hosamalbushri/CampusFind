@props([
    'name'        => 'tags',
    'value'       => [],
    'placeholder' => null,
])

<v-tags
    name="{{ $name }}"
    :value='@json($value)'
    placeholder="{{ $placeholder ?? trans('campusfind_web_web::app.web.search') }}"
    {{ $attributes }}
>
</v-tags>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-tags-template"
    >
        <div class="w-full font-cairo">
            <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 p-2 min-h-[46px] focus-within:border-[#185c54] focus-within:ring-4 focus-within:ring-[#185c54]/15 transition-all">
                <!-- Added Tag Chips -->
                <span
                    v-for="(tag, index) in tags"
                    :key="index"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-[#e6f4ee] dark:bg-[#185c54]/30 text-[#185c54] dark:text-[#a3e4c8] px-2.5 py-1 text-xs font-bold"
                >
                    @{{ tag }}
                    <button
                        type="button"
                        class="hover:text-rose-600 dark:hover:text-rose-400 cursor-pointer border-0 bg-transparent p-0 flex items-center"
                        @click="removeTag(index)"
                    >
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <input type="hidden" :name="`${name}[]`" :value="tag" />
                </span>

                <!-- Text Input for New Tags -->
                <input
                    type="text"
                    v-model="newTag"
                    :placeholder="tags.length === 0 ? placeholder : ''"
                    class="flex-1 bg-transparent text-sm text-slate-900 dark:text-white placeholder-slate-400 border-0 outline-none min-w-[120px] px-1 py-0.5"
                    @keydown.enter.prevent="addTag"
                    @keydown.,.prevent="addTag"
                    @keydown.backspace="handleBackspace"
                />
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-tags', {
            template: '#v-tags-template',

            props: {
                name: {
                    type: String,
                    default: 'tags',
                },
                value: {
                    type: [Array, String],
                    default: () => [],
                },
                placeholder: {
                    type: String,
                    default: '',
                },
            },

            data() {
                return {
                    tags: Array.isArray(this.value) ? [...this.value] : (this.value ? [this.value] : []),
                    newTag: '',
                };
            },

            methods: {
                addTag() {
                    const tag = this.newTag.trim();
                    if (tag && ! this.tags.includes(tag)) {
                        this.tags.push(tag);
                        this.$emit('change', this.tags);
                    }
                    this.newTag = '';
                },

                removeTag(index) {
                    this.tags.splice(index, 1);
                    this.$emit('change', this.tags);
                },

                handleBackspace() {
                    if (this.newTag === '' && this.tags.length > 0) {
                        this.removeTag(this.tags.length - 1);
                    }
                },
            },
        });
    </script>
@endPushOnce
