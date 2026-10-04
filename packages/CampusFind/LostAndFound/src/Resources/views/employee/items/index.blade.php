<x-admin::layouts>
    <x-slot:title>
        @lang('lost_found::app.employee.items.title')
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    <x-admin::breadcrumbs name="admin.lost_found.items.index" />
                </div>

                <div class="text-xl font-bold dark:text-white">
                    @lang('lost_found::app.employee.items.title')
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                @if (bouncer()->hasPermission('lost_found.items.create'))
                    <button
                        type="button"
                        class="primary-button"
                        @click="$refs.lostFoundManager.openCreateModal()"
                    >
                        @lang('lost_found::app.employee.items.create_btn')
                    </button>
                @endif
            </div>
        </div>

        <v-lost-found-items
            ref="lostFoundManager"
            :categories='@json($categories ?? [])'
            :staff-users='@json($staffUsers ?? [])'
        >
            <x-admin::shimmer.datagrid />
        </v-lost-found-items>
    </div>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="lost-found-items-template"
        >
            <div>
                <!-- Datagrid -->
                <x-admin::datagrid
                    :src="route('admin.lost_found.items.index')"
                    ref="datagrid"
                />

                <!-- Create Item Modal -->
                <x-admin::form
                    v-slot="{ meta, values, errors, handleSubmit }"
                    as="div"
                >
                    <form
                        @submit="handleSubmit($event, storeItem)"
                        ref="createItemForm"
                    >
                        <x-admin::modal ref="createItemModal">
                            <x-slot:header>
                                <p class="text-lg font-bold text-gray-800 dark:text-white">
                                    @lang('lost_found::app.employee.items.create_title')
                                </p>
                            </x-slot>

                            <x-slot:content>
                                <div class="grid gap-4">
                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label class="required">
                                            @lang('lost_found::app.employee.items.form.title')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="title"
                                            rules="required|max:255"
                                            :label="trans('lost_found::app.employee.items.form.title')"
                                            :placeholder="trans('lost_found::app.employee.items.form.title')"
                                        />

                                        <x-admin::form.control-group.error control-name="title" />
                                    </x-admin::form.control-group>

                                    <div class="grid grid-cols-2 gap-4">
                                        <x-admin::form.control-group>
                                            <x-admin::form.control-group.label class="required">
                                                @lang('lost_found::app.employee.items.form.category')
                                            </x-admin::form.control-group.label>

                                            <x-admin::form.control-group.control
                                                type="select"
                                                name="category_id"
                                                rules="required"
                                                :label="trans('lost_found::app.employee.items.form.category')"
                                            >
                                                <option value="">@lang('lost_found::app.employee.items.form.select_category')</option>
                                                <option
                                                    v-for="cat in categories"
                                                    :key="cat.id"
                                                    :value="cat.id"
                                                >
                                                    @{{ cat.code }}
                                                </option>
                                            </x-admin::form.control-group.control>

                                            <x-admin::form.control-group.error control-name="category_id" />
                                        </x-admin::form.control-group>

                                        <x-admin::form.control-group>
                                            <x-admin::form.control-group.label class="required">
                                                @lang('lost_found::app.employee.items.form.found_location')
                                            </x-admin::form.control-group.label>

                                            <x-admin::form.control-group.control
                                                type="text"
                                                name="found_location"
                                                rules="required|max:255"
                                                :label="trans('lost_found::app.employee.items.form.found_location')"
                                                :placeholder="trans('lost_found::app.employee.items.form.found_location')"
                                            />

                                            <x-admin::form.control-group.error control-name="found_location" />
                                        </x-admin::form.control-group>
                                    </div>

                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label class="required">
                                            @lang('lost_found::app.employee.items.form.found_at')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="datetime"
                                            name="found_at"
                                            rules="required"
                                            :label="trans('lost_found::app.employee.items.form.found_at')"
                                            :placeholder="trans('lost_found::app.employee.items.form.found_at')"
                                        />

                                        <x-admin::form.control-group.error control-name="found_at" />
                                    </x-admin::form.control-group>

                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label>
                                            @lang('lost_found::app.employee.items.form.description')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="textarea"
                                            name="description"
                                            :label="trans('lost_found::app.employee.items.form.description')"
                                            :placeholder="trans('lost_found::app.employee.items.form.description')"
                                            rows="3"
                                        />

                                        <x-admin::form.control-group.error control-name="description" />
                                    </x-admin::form.control-group>

                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label>
                                            @lang('lost_found::app.employee.items.form.distinguishing_marks')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="textarea"
                                            name="distinguishing_marks"
                                            :label="trans('lost_found::app.employee.items.form.distinguishing_marks')"
                                            :placeholder="trans('lost_found::app.employee.items.form.distinguishing_marks')"
                                            rows="2"
                                        />

                                        <x-admin::form.control-group.error control-name="distinguishing_marks" />
                                    </x-admin::form.control-group>

                                </div>
                            </x-slot>

                            <x-slot:footer>
                                <x-admin::button
                                    button-type="submit"
                                    class="primary-button justify-center"
                                    :title="trans('lost_found::app.employee.items.save_btn')"
                                    ::loading="isProcessing"
                                    ::disabled="isProcessing"
                                />
                            </x-slot>
                        </x-admin::modal>
                    </form>
                </x-admin::form>
            </div>
        </script>

        <script type="module">
            app.component('v-lost-found-items', {
                template: '#lost-found-items-template',

                props: {
                    categories: {
                        type: Array,
                        default: () => []
                    },
                    staffUsers: {
                        type: Array,
                        default: () => []
                    }
                },

                data() {
                    return {
                        isProcessing: false,
                    };
                },

                methods: {
                    openCreateModal() {
                        this.$refs.createItemModal.open();
                    },

                    storeItem(params, { resetForm, setErrors }) {
                        const formData = new FormData(this.$refs.createItemForm);
                        this.isProcessing = true;

                        this.$axios.post("{{ route('admin.lost_found.items.store') }}", formData)
                            .then(response => {
                                this.isProcessing = false;
                                this.$refs.createItemModal.close();
                                this.$emitter.emit('add-flash', {
                                    type: 'success',
                                    message: response.data.message
                                });
                                this.$refs.datagrid.get();
                                resetForm();
                            })
                            .catch(error => {
                                this.isProcessing = false;
                                if (error.response && error.response.status === 422) {
                                    setErrors(error.response.data.errors);
                                } else {
                                    this.$emitter.emit('add-flash', {
                                        type: 'error',
                                        message: error.response?.data?.message || 'Error occurred'
                                    });
                                }
                            });
                    }
                }
            });
        </script>
    @endPushOnce
</x-admin::layouts>
