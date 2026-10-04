<?php

namespace CampusFind\LostAndFound\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use CampusFind\LostAndFound\DataGrids\Employee\LostReportDataGrid;
use CampusFind\LostAndFound\Enums\ReportStatus;
use CampusFind\LostAndFound\Models\LostReport;
use CampusFind\LostAndFound\Services\Application\LostAndFoundAuthorization;
use CampusFind\LostAndFound\Services\ReportStateService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class EmployeeReportReadController extends Controller
{
    public function index(): View|JsonResponse
    {
        LostAndFoundAuthorization::authorizeUser(auth('user')->user(), 'lost_found.items.view');

        if (request()->ajax()) {
            return app(LostReportDataGrid::class)->toJson();
        }

        return view('lost_found::employee.reports.index');
    }

    public function approve(int $id): JsonResponse
    {
        $actor = auth('user')->user();
        LostAndFoundAuthorization::authorizeUser($actor, 'lost_found.items.edit');

        $report = LostReport::findOrFail($id);

        if ($report->status !== ReportStatus::DRAFT) {
            return response()->json([
                'message' => trans('lost_found::app.admin.reports.already_processed'),
            ], 422);
        }

        $report->status = ReportStateService::transition($report->status, ReportStatus::ACTIVE);
        $report->submitted_at = now();
        $report->save();

        return response()->json([
            'message' => trans('lost_found::app.admin.reports.approved_success'),
            'data' => [
                'id' => $report->id,
                'status' => $report->status->value,
            ],
        ], 200);
    }

    public function reject(int $id): JsonResponse
    {
        $actor = auth('user')->user();
        LostAndFoundAuthorization::authorizeUser($actor, 'lost_found.items.edit');

        $report = LostReport::findOrFail($id);

        if ($report->status !== ReportStatus::DRAFT) {
            return response()->json([
                'message' => trans('lost_found::app.admin.reports.already_processed'),
            ], 422);
        }

        $report->status = ReportStateService::transition($report->status, ReportStatus::CANCELLED);
        $report->closed_at = now();
        $report->save();

        return response()->json([
            'message' => trans('lost_found::app.admin.reports.rejected_success'),
            'data' => [
                'id' => $report->id,
                'status' => $report->status->value,
            ],
        ], 200);
    }
}
