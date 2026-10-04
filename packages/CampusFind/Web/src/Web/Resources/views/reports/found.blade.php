<x-web::layouts>
    <x-web::container class="max-w-4xl py-10 sm:py-12">
        <!-- Breadcrumbs Navigation -->
        <x-web::breadcrumbs
            :items="[trans('campusfind_web_web::app.web.reports.report_found_title') => '']"
            class="mb-8"
        />

        <!-- Flash Session Alerts -->
        <x-web::flash-group class="mb-8" />

        <!-- Header Section -->
        <div class="mb-10 text-start">
            <div class="mb-3">
                <x-web::badge variant="mint" size="md">
                    @lang('campusfind_web_web::app.web.reports.report_found')
                </x-web::badge>
            </div>
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                @lang('campusfind_web_web::app.web.reports.report_found_title')
            </h1>
            <p class="mt-2 text-base text-slate-600 dark:text-slate-400 leading-relaxed max-w-2xl">
                @lang('campusfind_web_web::app.web.reports.report_found_subtitle')
            </p>
        </div>

        <!-- Found Item Form Card -->
        <x-web::card variant="elevated" padding="lg">
            <v-found-report-form>
                <div class="space-y-6 animate-pulse">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="h-10 bg-slate-200 dark:bg-slate-700 rounded-xl"></div>
                        <div class="h-10 bg-slate-200 dark:bg-slate-700 rounded-xl"></div>
                    </div>
                    <div class="h-24 bg-slate-200 dark:bg-slate-700 rounded-xl"></div>
                    <div class="h-10 bg-[#185c54]/20 rounded-xl w-40"></div>
                </div>
            </v-found-report-form>
        </x-web::card>

        <!-- Handover Custody Desks Info Card -->
        <x-web::card variant="flat" padding="lg" class="mt-8">
            <div class="mb-6">
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    @lang('campusfind_web_web::app.web.reports.handover_title')
                </h3>
                <p class="mt-1.5 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    @lang('campusfind_web_web::app.web.reports.handover_subtitle')
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
                    <div class="h-10 w-10 rounded-xl bg-[#e6f4ee] text-[#185c54] dark:bg-[#185c54]/30 dark:text-[#a3e4c8] flex items-center justify-center font-bold mb-3">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">
                        @lang('campusfind_web_web::app.web.reports.security_office')
                    </h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                        @lang('campusfind_web_web::app.web.reports.security_desc')
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
                    <div class="h-10 w-10 rounded-xl bg-[#e6f4ee] text-[#185c54] dark:bg-[#185c54]/30 dark:text-[#a3e4c8] flex items-center justify-center font-bold mb-3">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">
                        @lang('campusfind_web_web::app.web.reports.student_affairs')
                    </h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                        @lang('campusfind_web_web::app.web.reports.student_affairs_desc')
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
                    <div class="h-10 w-10 rounded-xl bg-[#e6f4ee] text-[#185c54] dark:bg-[#185c54]/30 dark:text-[#a3e4c8] flex items-center justify-center font-bold mb-3">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">
                        @lang('campusfind_web_web::app.web.reports.library_desk')
                    </h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                        @lang('campusfind_web_web::app.web.reports.library_desc')
                    </p>
                </div>
            </div>
        </x-web::card>
    </x-web::container>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-found-report-form-template"
        >
            <x-web::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
            >
                <form
                    @submit="handleSubmit($event, store)"
                    ref="foundReportForm"
                    class="space-y-6"
                >
                    <!-- Row 1: Category & Title -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Category -->
                        <x-web::form.control-group>
                            <x-web::form.control-group.label for="category_id" :required="true">
                                @lang('campusfind_web_web::app.web.reports.category')
                            </x-web::form.control-group.label>

                            <x-web::form.control-group.control
                                type="select"
                                name="category_id"
                                id="category_id"
                                rules="required"
                                :label="trans('campusfind_web_web::app.web.reports.category')"
                                :required="true"
                            >
                                <option value="">@lang('campusfind_web_web::app.web.reports.select_category')</option>
                                @foreach ($categories as $cat)
                                    @php
                                        $catId = is_object($cat) ? ($cat->id ?? '') : ($cat['id'] ?? '');
                                        $catName = is_object($cat) ? ($cat->name ?? $cat->code ?? '') : ($cat['name'] ?? $cat['code'] ?? '');
                                        if (empty($catName) && is_object($cat) && isset($cat->code)) {
                                            $catName = $cat->code;
                                        }
                                    @endphp
                                    <option value="{{ $catId }}">
                                        {{ ucwords(str_replace(['_', '-'], ' ', (string) $catName)) }}
                                    </option>
                                @endforeach
                            </x-web::form.control-group.control>

                            <x-web::form.control-group.error name="category_id" />
                        </x-web::form.control-group>

                        <!-- Title -->
                        <x-web::form.control-group>
                            <x-web::form.control-group.label for="title" :required="true">
                                @lang('campusfind_web_web::app.web.reports.title')
                            </x-web::form.control-group.label>

                            <x-web::form.control-group.control
                                type="text"
                                name="title"
                                id="title"
                                rules="required|max:160"
                                :label="trans('campusfind_web_web::app.web.reports.title')"
                                :required="true"
                                placeholder="{{ trans('campusfind_web_web::app.web.reports.title_found_placeholder') }}"
                            />

                            <x-web::form.control-group.error name="title" />
                        </x-web::form.control-group>
                    </div>

                    <!-- Row 2: Location & Date -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Location Found -->
                        <x-web::form.control-group>
                            <x-web::form.control-group.label for="found_location" :required="true">
                                @lang('campusfind_web_web::app.web.reports.found_location')
                            </x-web::form.control-group.label>

                            <x-web::form.control-group.control
                                type="text"
                                name="found_location"
                                id="found_location"
                                rules="required|max:255"
                                :label="trans('campusfind_web_web::app.web.reports.found_location')"
                                :required="true"
                                placeholder="{{ trans('campusfind_web_web::app.web.reports.found_location_placeholder') }}"
                            />

                            <x-web::form.control-group.error name="found_location" />
                        </x-web::form.control-group>

                        <!-- Date & Time Found -->
                        <x-web::form.control-group>
                            <x-web::form.control-group.label for="found_at">
                                @lang('campusfind_web_web::app.web.reports.found_date')
                            </x-web::form.control-group.label>

                            <x-web::form.control-group.control
                                type="datetime-local"
                                name="found_at"
                                id="found_at"
                                :label="trans('campusfind_web_web::app.web.reports.found_date')"
                            />

                            <x-web::form.control-group.error name="found_at" />
                        </x-web::form.control-group>
                    </div>

                    <!-- Custody Handover Point -->
                    <x-web::form.control-group>
                        <x-web::form.control-group.label for="dropoff_location">
                            @lang('campusfind_web_web::app.web.reports.dropoff_location')
                        </x-web::form.control-group.label>

                        <x-web::form.control-group.control
                            type="select"
                            name="dropoff_location"
                            id="dropoff_location"
                            :label="trans('campusfind_web_web::app.web.reports.dropoff_location')"
                        >
                            <option value="">@lang('campusfind_web_web::app.web.reports.dropoff_placeholder')</option>
                            <option value="security_office">
                                @lang('campusfind_web_web::app.web.reports.security_office')
                            </option>
                            <option value="student_affairs">
                                @lang('campusfind_web_web::app.web.reports.student_affairs')
                            </option>
                            <option value="library_desk">
                                @lang('campusfind_web_web::app.web.reports.library_desk')
                            </option>
                        </x-web::form.control-group.control>

                        <x-web::form.control-group.error name="dropoff_location" />
                    </x-web::form.control-group>

                    <!-- Public Description -->
                    <x-web::form.control-group>
                        <x-web::form.control-group.label for="description">
                            @lang('campusfind_web_web::app.web.reports.public_description')
                        </x-web::form.control-group.label>

                        <x-web::form.control-group.control
                            type="textarea"
                            name="description"
                            id="description"
                            rows="3"
                            :label="trans('campusfind_web_web::app.web.reports.public_description')"
                            placeholder="{{ trans('campusfind_web_web::app.web.reports.public_description_placeholder') }}"
                        />

                        <x-web::form.control-group.error name="description" />
                    </x-web::form.control-group>

                    <!-- Photo Upload -->
                    <x-web::form.control-group>
                        <x-web::form.control-group.label for="image">
                            @lang('campusfind_web_web::app.web.reports.item_image')
                        </x-web::form.control-group.label>

                        <x-web::form.control-group.control
                            type="file"
                            name="image"
                            id="image"
                            accept="image/jpeg,image/png,image/webp"
                        />

                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            @lang('campusfind_web_web::app.web.reports.image_hint')
                        </p>

                        <x-web::form.control-group.error name="image" />
                    </x-web::form.control-group>

                    <!-- Actions -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <x-web::button
                            type="submit"
                            variant="primary"
                            size="xl"
                            class="w-full sm:w-auto shadow-md"
                            ::loading="isProcessing"
                        >
                            @lang('campusfind_web_web::app.web.reports.submit_found_report')
                        </x-web::button>

                        <x-web::button
                            href="{{ route('campusfind_web.web.items.index') }}"
                            variant="ghost"
                            size="md"
                        >
                            @lang('campusfind_web_web::app.web.back_to_items')
                        </x-web::button>
                    </div>
                </form>
            </x-web::form>
        </script>

        <script type="module">
            app.component('v-found-report-form', {
                template: '#v-found-report-form-template',

                data() {
                    return {
                        isProcessing: false,
                    };
                },

                methods: {
                    store(params, { resetForm, setErrors }) {
                        this.isProcessing = true;
                        const formData = new FormData(this.$refs.foundReportForm);

                        this.$axios.post("{{ route('campusfind_web.web.reports.found.store') }}", formData)
                            .then((response) => {
                                this.isProcessing = false;

                                if (response.data.message) {
                                    this.$emitter.emit('add-flash', {
                                        type: 'success',
                                        message: response.data.message
                                    });
                                }

                                if (response.data.redirect_url) {
                                    window.location.href = response.data.redirect_url;
                                }
                            })
                            .catch((error) => {
                                this.isProcessing = false;

                                if (error.response && error.response.status === 422) {
                                    setErrors(error.response.data.errors);
                                } else if (error.response && error.response.data && error.response.data.message) {
                                    this.$emitter.emit('add-flash', {
                                        type: 'error',
                                        message: error.response.data.message
                                    });
                                }
                            });
                    },
                },
            });
        </script>
    @endPushOnce
</x-web::layouts>
