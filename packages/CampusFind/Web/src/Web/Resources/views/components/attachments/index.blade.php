@props([
    'name'          => 'attachments',
    'allowMultiple' => true,
    'maxSize'       => '10MB',
])

<v-attachments
    name="{{ $name }}"
    :allow-multiple="{{ filter_var($allowMultiple, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false' }}"
    max-size="{{ $maxSize }}"
    {{ $attributes }}
>
</v-attachments>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-attachments-template"
    >
        <div class="font-cairo space-y-3">
            <!-- Dropzone Area -->
            <label class="group relative flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-[#185c54] dark:hover:border-[#a3e4c8] bg-slate-50 dark:bg-slate-800/60 p-6 transition-all cursor-pointer text-center">
                <div class="flex flex-col items-center gap-2 text-slate-500 dark:text-slate-400 group-hover:text-[#185c54] dark:group-hover:text-[#a3e4c8] transition-colors">
                    <svg class="h-8 w-8 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                    </svg>
                    <p class="text-sm font-bold">@lang('campusfind_web_web::app.web.reports.image_hint')</p>
                </div>

                <input
                    type="file"
                    class="hidden"
                    :name="`${name}[]`"
                    :multiple="allowMultiple"
                    @change="handleFiles"
                />
            </label>

            <!-- Attachment List -->
            <ul v-if="files.length > 0" class="divide-y divide-slate-100 dark:divide-slate-800 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 list-none p-0 m-0">
                <li
                    v-for="(file, index) in files"
                    :key="index"
                    class="flex items-center justify-between p-3 text-sm"
                >
                    <div class="flex items-center gap-2 truncate">
                        <svg class="h-4 w-4 text-[#185c54] dark:text-[#a3e4c8] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="font-medium text-slate-800 dark:text-slate-200 truncate">@{{ file.name }}</span>
                        <span class="text-xs text-slate-400 shrink-0">(@{{ formatSize(file.size) }})</span>
                    </div>

                    <button
                        type="button"
                        class="text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 cursor-pointer border-0 bg-transparent p-1"
                        @click="removeFile(index)"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </li>
            </ul>
        </div>
    </script>

    <script type="module">
        app.component('v-attachments', {
            template: '#v-attachments-template',

            props: {
                name: {
                    type: String,
                    default: 'attachments',
                },
                allowMultiple: {
                    type: Boolean,
                    default: true,
                },
                maxSize: {
                    type: String,
                    default: '10MB',
                },
            },

            data() {
                return {
                    files: [],
                };
            },

            methods: {
                handleFiles(event) {
                    const newFiles = Array.from(event.target.files);
                    if (this.allowMultiple) {
                        this.files.push(...newFiles);
                    } else {
                        this.files = newFiles;
                    }
                    this.$emit('change', this.files);
                },

                removeFile(index) {
                    this.files.splice(index, 1);
                    this.$emit('change', this.files);
                },

                formatSize(bytes) {
                    if (bytes === 0) return '0 B';
                    const k = 1024;
                    const sizes = ['B', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
                },
            },
        });
    </script>
@endPushOnce
