@props([
    'name'             => 'images',
    'allowMultiple'    => false,
    'uploadedImages'   => [],
    'width'            => '120px',
    'height'           => '120px',
])

<v-media-images
    name="{{ $name }}"
    :allow-multiple="{{ filter_var($allowMultiple, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false' }}"
    :uploaded-images='@json($uploadedImages)'
    width="{{ $width }}"
    height="{{ $height }}"
    {{ $attributes }}
>
    <x-web::shimmer.image class="h-[120px] w-[120px]" />
</v-media-images>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-media-images-template"
    >
        <div class="font-cairo">
            <div class="flex flex-wrap items-center gap-3">
                <!-- Upload Trigger Box -->
                <template v-if="allowMultiple || images.length === 0">
                    <label
                        class="group relative flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-[#185c54] dark:hover:border-[#a3e4c8] bg-slate-50 dark:bg-slate-800/60 transition-all cursor-pointer select-none p-4 text-center"
                        :style="{ width: width, height: height, minWidth: width, minHeight: height }"
                    >
                        <div class="flex flex-col items-center justify-center gap-1.5 text-slate-500 dark:text-slate-400 group-hover:text-[#185c54] dark:group-hover:text-[#a3e4c8] transition-colors">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-[11px] font-bold leading-tight">@lang('campusfind_web_web::app.web.images')</span>
                        </div>

                        <input
                            type="file"
                            class="hidden"
                            :name="name"
                            accept="image/jpeg,image/png,image/webp"
                            :multiple="allowMultiple"
                            @change="handleFileChange"
                        />
                    </label>
                </template>

                <!-- Uploaded / Selected Image Previews -->
                <div
                    v-for="(image, index) in images"
                    :key="index"
                    class="group relative overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 shadow-xs"
                    :style="{ width: width, height: height, minWidth: width, minHeight: height }"
                >
                    <img
                        :src="image.url"
                        alt="Preview"
                        class="h-full w-full object-cover rounded-2xl"
                    />

                    <!-- Remove Button Overlay -->
                    <button
                        type="button"
                        class="absolute top-1.5 ltr:right-1.5 rtl:left-1.5 rounded-lg bg-rose-600/90 text-white p-1 hover:bg-rose-700 transition-colors shadow-xs cursor-pointer border-0 focus:outline-none"
                        title="Remove image"
                        @click="remove(index)"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-media-images', {
            template: '#v-media-images-template',

            props: {
                name: {
                    type: String,
                    default: 'images',
                },
                allowMultiple: {
                    type: Boolean,
                    default: false,
                },
                uploadedImages: {
                    type: Array,
                    default: () => [],
                },
                width: {
                    type: String,
                    default: '120px',
                },
                height: {
                    type: String,
                    default: '120px',
                },
            },

            data() {
                return {
                    images: [],
                };
            },

            mounted() {
                if (this.uploadedImages && this.uploadedImages.length) {
                    this.images = this.uploadedImages.map(img => {
                        if (typeof img === 'string') {
                            return { url: img };
                        }
                        return img;
                    });
                }
            },

            methods: {
                handleFileChange(event) {
                    const files = event.target.files;
                    if (! files || ! files.length) return;

                    for (let i = 0; i < files.length; i++) {
                        const file = files[i];
                        const reader = new FileReader();

                        reader.onload = (e) => {
                            const newImage = {
                                url: e.target.result,
                                file: file,
                            };

                            if (this.allowMultiple) {
                                this.images.push(newImage);
                            } else {
                                this.images = [newImage];
                            }
                        };

                        reader.readAsDataURL(file);
                    }
                },

                remove(index) {
                    this.images.splice(index, 1);
                    this.$emit('remove', index);
                },
            },
        });
    </script>
@endPushOnce
