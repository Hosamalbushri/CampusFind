<?php

namespace CampusFind\LostAndFound\Http\Controllers\Employee;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use CampusFind\LostAndFound\DataGrids\Employee\FoundItemDataGrid;
use CampusFind\LostAndFound\Services\Application\LostAndFoundAuthorization;

class EmployeeItemReadController extends Controller
{
    public function index(): View|JsonResponse
    {
        LostAndFoundAuthorization::authorizeUser(auth('user')->user(), 'lost_found.items.view');

        if (request()->ajax()) {
            return datagrid(FoundItemDataGrid::class)->process();
        }

        $categories = DB::table('lost_found_categories')
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->get(['id', 'code']);

        $staffUsers = DB::table('users')
            ->where('status', 1)
            ->get(['id', 'name', 'email']);

        return view('lost_found::employee.items.index', compact('categories', 'staffUsers'));
    }
}
