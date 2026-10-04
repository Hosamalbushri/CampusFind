<v-control-tags
    :errors="errors"
    {{ $attributes }}
    v-bind="$attrs"
></v-control-tags>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-control-tags-template"
    >
        <div 
            class="flex min-h-[44px] w-full items-center rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 px-3.5 py-2 text-sm font-medium text-slate-800 dark:text-white transition-all hover:border-slate-400 focus-within:border-[#185c54] focus-within:ring-4 focus-within:ring-[#185c54]/15"
            :class="[errors[`temp-${name}`] ? 'border !border-rose-500 hover:border-rose-500' : '']"
        >
            <ul
                class="flex flex-wrap items-center gap-1.5"
                v-bind="$attrs"
            >
                <li
                    v-for="(tag, index) in tags"
                    :key="index"
                    class="flex items-center gap-1.5 rounded-lg bg-[#e6f4ee] dark:bg-[#185c54]/30 text-[#185c54] dark:text-[#a3e4c8] px-2.5 py-1 text-xs font-bold"
                >
                    <x-web::form.control-group.control
                        type="hidden"
                        ::name="name + '[' + index + ']'"
                        ::value="tag"
                    />

                    @{{ tag }}

                    <span
                        class="cursor-pointer p-0.5 text-xs hover:text-rose-600 dark:hover:text-rose-400"
                        @click="removeTag(tag)"
                    >✕</span>
                </li>

                <li :class="['w-full', tags.length && 'mt-1.5']">
                    <v-field
                        v-slot="{ field, errors }"
                        :name="'temp-' + name"
                        v-model="input"
                        :rules="tags.length ? inputRules : [inputRules, rules].filter(Boolean).join('|')"
                        :label="label"
                    >
                        <input
                            type="text"
                            :name="'temp-' + name"
                            v-bind="field"
                            class="w-full bg-transparent outline-none border-0 text-sm text-slate-900 dark:text-white placeholder-slate-400"
                            :placeholder="placeholder"
                            :label="label"
                            @keydown.enter.prevent="addTag"
                            autocomplete="off"
                            @blur="addTag"
                        />
                    </v-field>

                    <template v-if="! tags.length && input != ''">
                        <v-field
                            v-slot="{ field, errors }"
                            :name="name + '[' + 0 +']'"
                            :value="input"
                            :rules="inputRules"
                            :label="label"
                        >
                            <input
                                type="hidden"
                                :name="name + '[0]'"
                                v-bind="field"
                            />
                        </v-field>
                    </template>
                </li>
            </ul>
        </div>

        <v-error-message
            :name="'temp-' + name"
            v-slot="{ message }"
        >
            <p
                class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400"
                v-text="message"
            >
            </p>
        </v-error-message>
    </script>

    <script type="module">
        app.component('v-control-tags', {
            template: '#v-control-tags-template',

            props: {
                name: {
                    type: String,
                    required: true,
                },

                label: {
                    type: String,
                    default: '',
                },

                placeholder: {
                    type: String,
                    default: '',
                },

                rules: {
                    type: String,
                    default: '',
                },

                inputRules: {
                    type: String,
                    default: '',
                },

                data: {
                    type: Array,
                    default: () => [],
                },

                errors: {
                    type: Object,
                    default: () => ({}),
                },

                allowDuplicates: {
                    type: Boolean,
                    default: false,
                },
            },

            data() {
                return {
                    tags: Array.isArray(this.data) ? this.data : [],

                    input: '',
                };
            },

            methods: {
                addTag() {
                    if (this.errors && this.errors['temp-' + this.name]) {
                        return;
                    }

                    const tag = this.input.trim();

                    if (! tag) {
                        return;
                    }

                    if (
                        ! this.allowDuplicates
                        && this.tags.includes(tag)
                    ) {
                        this.input = '';

                        return;
                    }

                    this.tags.push(tag);

                    this.$emit('tags-updated', this.tags);

                    this.input = '';
                },

                removeTag(tag) {
                    this.tags = this.tags.filter(function (tempTag) {
                        return tempTag !== tag;
                    });

                    this.$emit('tags-updated', this.tags);
                },
            }
        });
    </script>
@endpushOnce
