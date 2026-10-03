<?php

namespace CampusFind\LostAndFound\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use CampusFind\LostAndFound\DataGrids\CategoryDataGrid;
use CampusFind\LostAndFound\Http\Requests\StoreCategoryRequest;
use CampusFind\LostAndFound\Http\Requests\UpdateCategoryRequest;
use CampusFind\LostAndFound\Repositories\LostFoundCategoryRepository;
use CampusFind\LostAndFound\Services\Application\EmployeeCategoryApplicationService;
use CampusFind\LostAndFound\Services\Application\LostAndFoundAuthorization;

class EmployeeCategoryController extends Controller
{
    public function __construct(
        protected EmployeeCategoryApplicationService $categoryService,
        protected LostFoundCategoryRepository $categoryRepository
    ) {}

    public function index(): View|JsonResponse
    {
        LostAndFoundAuthorization::authorizeUser(auth('user')->user(), 'lost_found.settings.categories');

        if (request()->ajax()) {
            return datagrid(CategoryDataGrid::class)->process();
        }

        return view('lost_found::employee.categories.index');
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $actor = auth('user')->user();

        $category = $this->categoryService->createCategory($actor, $request->validated());

        return response()->json([
            'message' => trans('lost_found::app.employee.categories.created_success'),
            'data'    => $category,
        ], 201);
    }

    public function edit(int $id): JsonResponse
    {
        LostAndFoundAuthorization::authorizeUser(auth('user')->user(), 'lost_found.settings.categories');

        $category = $this->categoryRepository->findOrFail($id);

        return response()->json([
            'data' => $category,
        ]);
    }

    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        $actor = auth('user')->user();

        $category = $this->categoryService->updateCategory($actor, $id, $request->validated());

        return response()->json([
            'message' => trans('lost_found::app.employee.categories.updated_success'),
            'data'    => $category,
        ]);
    }
}
