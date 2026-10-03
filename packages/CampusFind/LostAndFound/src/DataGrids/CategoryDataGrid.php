<?php

namespace CampusFind\LostAndFound\DataGrids;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;
use CampusFind\LostAndFound\Services\Application\LostAndFoundAuthorization;

class CategoryDataGrid extends DataGrid
{
    protected $primaryColumn = 'id';

    public function prepareQueryBuilder(): Builder
    {
        LostAndFoundAuthorization::authorizeUser(auth('user')->user(), 'lost_found.settings.categories');

        return DB::table('lost_found_categories')->select([
            'id',
            'code',
            'is_active',
            'sort_order',
            'created_at',
        ]);
    }

    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('lost_found::app.employee.items.id'),
            'type'       => 'integer',
            'sortable'   => true,
            'searchable' => false,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'code',
            'label'      => trans('lost_found::app.employee.categories.code'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'              => 'is_active',
            'label'              => trans('lost_found::app.employee.categories.is_active'),
            'type'               => 'boolean',
            'sortable'           => true,
            'searchable'         => false,
            'filterable'         => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => [
                ['label' => trans('lost_found::app.employee.categories.active'), 'value' => 1],
                ['label' => trans('lost_found::app.employee.categories.inactive'), 'value' => 0],
            ],
            'closure'            => static fn ($row): string => $row->is_active
                ? '<span class="label-active">'.e(trans('lost_found::app.employee.categories.active')).'</span>'
                : '<span class="label-inactive">'.e(trans('lost_found::app.employee.categories.inactive')).'</span>',
        ]);

        $this->addColumn([
            'index'      => 'sort_order',
            'label'      => trans('lost_found::app.employee.categories.sort_order'),
            'type'       => 'integer',
            'sortable'   => true,
            'searchable' => false,
            'filterable' => false,
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => trans('lost_found::app.employee.categories.created_at'),
            'type'       => 'date',
            'sortable'   => true,
            'searchable' => false,
            'filterable' => true,
        ]);
    }
}
