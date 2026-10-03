<?php

namespace CampusFind\LostAndFound\DataGrids;

use CampusFind\LostAndFound\Enums\ClaimStatus;
use CampusFind\LostAndFound\Services\Application\LostAndFoundAuthorization;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class AllClaimDataGrid extends DataGrid
{
    protected $primaryColumn = 'id';

    public function prepareQueryBuilder(): Builder
    {
        LostAndFoundAuthorization::authorizeUser(auth('user')->user(), 'lost_found.claims.view');

        return DB::table('lost_found_claims')
            ->join('students', 'lost_found_claims.claimant_student_id', '=', 'students.id')
            ->join('lost_found_items', 'lost_found_claims.found_item_id', '=', 'lost_found_items.id')
            ->select([
                'lost_found_claims.id',
                'lost_found_claims.status',
                'lost_found_claims.submitted_at',
                'students.name as claimant_name',
                'lost_found_items.public_reference as item_reference',
                'lost_found_items.title as item_title',
            ]);
    }

    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('lost_found::app.employee.claims.id'),
            'type'       => 'integer',
            'sortable'   => true,
            'searchable' => false,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'item_reference',
            'label'      => trans('lost_found::app.employee.items.public_reference'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'item_title',
            'label'      => trans('lost_found::app.employee.items.title_column'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'claimant_name',
            'label'      => trans('lost_found::app.employee.claims.claimant_name'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'              => 'status',
            'label'              => trans('lost_found::app.employee.claims.status'),
            'type'               => 'string',
            'sortable'           => true,
            'searchable'         => false,
            'filterable'         => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => array_map(static fn (ClaimStatus $status): array => [
                'label' => trans("lost_found::app.employee.claims.statuses.{$status->value}"),
                'value' => $status->value,
            ], ClaimStatus::cases()),
            'closure'            => static fn ($row): string => e(trans("lost_found::app.employee.claims.statuses.{$row->status}")),
        ]);

        $this->addColumn([
            'index'      => 'submitted_at',
            'label'      => trans('lost_found::app.employee.claims.submitted_at'),
            'type'       => 'date',
            'sortable'   => true,
            'searchable' => false,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'   => 'actions',
            'label'   => trans('lost_found::app.employee.items.actions'),
            'type'    => 'string',
            'closure' => static fn ($row): string => '<a href="'.e(route('admin.lost_found.claims.show', (int) $row->id)).'" class="primary-button text-xs py-1 px-2">'.e(trans('lost_found::app.employee.claims.view_detail')).'</a>',
        ]);
    }
}
