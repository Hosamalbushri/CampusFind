<x-admin::layouts>
    <x-slot:title>
        @lang('lost_found::app.employee.reports.title')
    </x-slot>

    <div class="flex flex-col gap-4">
        <!-- Header -->
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    <x-admin::breadcrumbs name="admin.lost_found.reports.index" />
                </div>

                <div class="text-xl font-bold dark:text-white">
                    @lang('lost_found::app.employee.reports.title')
                </div>
            </div>
        </div>

        <!-- DataGrid -->
        <x-admin::datagrid :src="route('admin.lost_found.reports.index')" />
    </div>
</x-admin::layouts>
