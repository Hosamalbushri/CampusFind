<x-admin::layouts>
    <x-slot:title>
        @lang('lost_found::app.employee.categories.title')
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    <x-admin::breadcrumbs name="admin.lost_found.settings.categories.index" />
                </div>

                <div class="text-xl font-bold dark:text-white">
                    @lang('lost_found::app.employee.categories.title')
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                @if (bouncer()->hasPermission('lost_found.settings.categories'))
                    <button
                        type="button"
                        class="primary-button"
                        @click="$refs.categoryManager.openCreateModal()"
                    >
                        @lang('lost_found::app.employee.categories.create_btn')
                    </button>
                @endif
            </div>
        </div>

        <v-lost-found-categories ref="categoryManager">
            <x-admin::datagrid :src="route('admin.lost_found.settings.categories.index')">
                <x-admin::shimmer.datagrid />
            </x-admin::datagrid>
        </v-lost-found-categories>
    </div>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="lost-found-categories-template"
        >
            <div>
                <!-- Datagrid -->
                <x-admin::datagrid
                    :src="route('admin.lost_found.settings.categories.index')"
                    ref="datagrid"
                />

                <!-- Category Modal (Create / Edit) -->
                <x-admin::form
                    v-slot="{ meta, values, errors, handleSubmit }"
                    as="div"
                >
                    <form
                        @submit="handleSubmit($event, saveCategory)"
                        ref="categoryForm"
                    >
                        <x-admin::modal ref="categoryModal">
                            <x-slot:header>
                                <p class="text-lg font-bold text-gray-800 dark:text-white">
                                    @{{ selectedCategory.id ? "@lang('lost_found::app.employee.categories.edit_title')" : "@lang('lost_found::app.employee.categories.create_title')" }}
                                </p>
                            </x-slot>

                            <x-slot:content>
                                <div class="grid gap-4">
                                    <input
                                        type="hidden"
                                        name="id"
                                        v-model="selectedCategory.id"
                                    />

                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label class="required">
                                            @lang('lost_found::app.employee.categories.code')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="code"
                                            rules="required|max:64"
                                            v-model="selectedCategory.code"
                                            :label="trans('lost_found::app.employee.categories.code')"
                                            :placeholder="trans('lost_found::app.employee.categories.code')"
                                        />

                                        <x-admin::form.control-group.error control-name="code" />
                                    </x-admin::form.control-group>

                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label>
                                            @lang('lost_found::app.employee.categories.sort_order')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="number"
                                            name="sort_order"
                                            rules="min:0"
                                            v-model="selectedCategory.sort_order"
                                            :label="trans('lost_found::app.employee.categories.sort_order')"
                                        />

                                        <x-admin::form.control-group.error control-name="sort_order" />
                                    </x-admin::form.control-group>

                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label>
                                            @lang('lost_found::app.employee.categories.is_active')
                                        </x-admin::form.control-group.label>

                                        <input
                                            type="hidden"
                                            name="is_active"
                                            :value="0"
                                        />

                                        <x-admin::form.control-group.control
                                            type="switch"
                                            name="is_active"
                                            value="1"
                                            :label="trans('lost_found::app.employee.categories.is_active')"
                                            ::checked="Boolean(selectedCategory.is_active)"
                                        />

                                        <x-admin::form.control-group.error control-name="is_active" />
                                    </x-admin::form.control-group>
                                </div>
                            </x-slot>

                            <x-slot:footer>
                                <x-admin::button
                                    button-type="submit"
                                    class="primary-button justify-center"
                                    :title="trans('lost_found::app.employee.categories.save_btn')"
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
            app.component('v-lost-found-categories', {
                template: '#lost-found-categories-template',

                data() {
                    return {
                        isProcessing: false,
                        selectedCategory: {
                            id: null,
                            code: '',
                            sort_order: 0,
                            is_active: 1
                        }
                    };
                },

                methods: {
                    openCreateModal() {
                        this.selectedCategory = {
                            id: null,
                            code: '',
                            sort_order: 0,
                            is_active: 1
                        };
                        this.$refs.categoryModal.open();
                    },

                    openEditModal(category) {
                        this.selectedCategory = { ...category };
                        this.$refs.categoryModal.open();
                    },

                    saveCategory(params, { resetForm, setErrors }) {
                        this.isProcessing = true;
                        const isUpdate = Boolean(this.selectedCategory.id);
                        const url = isUpdate
                            ? "{{ route('admin.lost_found.settings.categories.update', ':id') }}".replace(':id', this.selectedCategory.id)
                            : "{{ route('admin.lost_found.settings.categories.store') }}";

                        const formData = new FormData(this.$refs.categoryForm);
                        if (isUpdate) {
                            formData.append('_method', 'PUT');
                        }

                        this.$axios.post(url, formData)
                            .then(response => {
                                this.isProcessing = false;
                                this.$refs.categoryModal.close();
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
