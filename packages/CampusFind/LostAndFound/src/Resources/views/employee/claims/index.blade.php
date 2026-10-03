<x-admin::layouts>
    <x-slot:title>
        @lang('lost_found::app.employee.claims.title')
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    <x-admin::breadcrumbs name="admin.lost_found.items.claims.index" :entity="$item->id" />
                </div>

                <div class="text-xl font-bold dark:text-white">
                    @lang('lost_found::app.employee.claims.title')
                </div>
                <div class="text-xs text-gray-500">
                    {{ $item->public_reference }} — {{ $item->title }} ({{ trans('lost_found::app.employee.items.statuses.'.$item->status) }})
                </div>
            </div>
        </div>

        <x-admin::datagrid :src="route('admin.lost_found.items.claims.index', $item->id)">
            <x-admin::shimmer.datagrid />
        </x-admin::datagrid>
    </div>
</x-admin::layouts>
