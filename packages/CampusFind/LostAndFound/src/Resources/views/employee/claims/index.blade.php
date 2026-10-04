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
                <div class="flex items-center gap-2 mt-1 text-xs">
                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ $item->public_reference }} — {{ $item->title }}</span>
                    <span class="badge badge-sm badge-success font-medium">@lang('lost_found::app.types.found')</span>
                    @php
                        $itemStatusClass = match($item->status ?? '') {
                            'reported' => 'badge-info',
                            'in_custody' => 'badge-warning',
                            'handover_in_progress' => 'badge-secondary',
                            'claimed' => 'badge-success',
                            'disposed' => 'badge-danger',
                            default => 'badge-secondary',
                        };
                    @endphp
                    <span class="badge badge-sm {{ $itemStatusClass }} font-medium">{{ trans('lost_found::app.employee.items.statuses.'.$item->status) }}</span>
                </div>
            </div>
        </div>

        <x-admin::datagrid :src="route('admin.lost_found.items.claims.index', $item->id)" />
    </div>
</x-admin::layouts>
