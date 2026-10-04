<?php

namespace CampusFind\Web\Web\Http\Controllers;

use CampusFind\LostAndFound\Models\LostFoundCategory;
use CampusFind\LostAndFound\Models\LostReport;
use CampusFind\LostAndFound\Services\Application\PublicFoundItemIntakeApplicationService;
use CampusFind\LostAndFound\Services\Application\StudentReportApplicationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    /**
     * Show the form for reporting a lost item.
     */
    public function createLost(Request $request): View
    {
        $this->handleLocale($request);

        $categories = $this->getCategories();
        $isStudent = auth()->guard('student')->check();
        $student = $isStudent ? auth()->guard('student')->user() : null;

        return view('campusfind_web_web::reports.lost', [
            'categories' => $categories,
            'isStudent' => $isStudent,
            'student' => $student,
        ]);
    }

    /**
     * Store a newly submitted lost item report.
     */
    public function storeLost(Request $request): JsonResponse|RedirectResponse
    {
        $this->handleLocale($request);

        if (! auth()->guard('student')->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message'      => trans('campusfind_web_web::app.web.reports.login_required_desc'),
                    'redirect_url' => route('campusfind_web.web.login'),
                ], 401);
            }

            return redirect()->route('campusfind_web.web.login')
                ->with('error', trans('campusfind_web_web::app.web.reports.login_required_desc'));
        }

        // Auto-resolve string category code (e.g. 'electronics') to integer ID if passed
        if ($request->filled('category_id') && ! is_numeric($request->input('category_id')) && class_exists(LostFoundCategory::class)) {
            $catId = LostFoundCategory::where('code', $request->input('category_id'))->value('id');
            if ($catId) {
                $request->merge(['category_id' => $catId]);
            }
        }

        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:lost_found_categories,id'],
            'title' => ['required', 'string', 'max:160'],
            'lost_location' => ['nullable', 'string', 'max:255'],
            'lost_at' => ['nullable', 'date'],
            'public_description' => ['nullable', 'string'],
            'private_description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,webp,jpg', 'max:5120'],
        ]);

        $student = auth()->guard('student')->user();
        $report = null;

        if (class_exists(StudentReportApplicationService::class)) {
            /** @var StudentReportApplicationService $service */
            $service = app(StudentReportApplicationService::class);

            $report = $service->createLostReport($student, [
                'category_id' => (int) $validated['category_id'],
                'title' => $validated['title'],
                'lost_location' => $validated['lost_location'] ?? null,
                'lost_at' => $validated['lost_at'] ?? null,
                'public_description' => $validated['public_description'] ?? null,
                'private_description' => $validated['private_description'] ?? null,
            ]);

            if ($request->hasFile('image')) {
                $service->addOwnLostReportImage($student, $report, $request->file('image'));
            }
        } elseif (class_exists(LostReport::class)) {
            $report = LostReport::create([
                'student_id' => $student->id,
                'category_id' => (int) $validated['category_id'],
                'title' => $validated['title'],
                'lost_location' => $validated['lost_location'] ?? null,
                'lost_at' => $validated['lost_at'] ?? null,
                'public_description' => $validated['public_description'] ?? null,
                'private_description' => $validated['private_description'] ?? null,
                'status' => 'draft',
                'public_reference' => 'LR-'.strtoupper(Str::random(10)),
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message'      => trans('campusfind_web_web::app.web.reports.success_lost_created'),
                'redirect_url' => route('campusfind_web.web.account.dashboard'),
                'data'         => [
                    'id' => $report->id ?? null,
                    'reference' => $report->public_reference ?? null,
                ],
            ], 201);
        }

        return redirect()->route('campusfind_web.web.account.dashboard')
            ->with('success', trans('campusfind_web_web::app.web.reports.success_lost_created'));
    }

    /**
     * Show the form for reporting a found item.
     */
    public function createFound(Request $request): View
    {
        $this->handleLocale($request);

        $categories = $this->getCategories();
        $isStudent = auth()->guard('student')->check();
        $student = $isStudent ? auth()->guard('student')->user() : null;

        return view('campusfind_web_web::reports.found', [
            'categories' => $categories,
            'isStudent' => $isStudent,
            'student' => $student,
        ]);
    }

    /**
     * Store a newly submitted found item notification.
     */
    public function storeFound(Request $request, PublicFoundItemIntakeApplicationService $service): JsonResponse|RedirectResponse
    {
        $this->handleLocale($request);

        // Auto-resolve string category code to integer ID if passed
        if ($request->filled('category_id') && ! is_numeric($request->input('category_id')) && class_exists(LostFoundCategory::class)) {
            $catId = LostFoundCategory::where('code', $request->input('category_id'))->value('id');
            if ($catId) {
                $request->merge(['category_id' => $catId]);
            }
        }

        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:lost_found_categories,id'],
            'title' => ['required', 'string', 'max:160'],
            'found_location' => ['required', 'string', 'max:255'],
            'found_at' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
            'dropoff_location' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,webp,jpg', 'max:5120'],
        ]);

        $item = $service->submit(
            auth('student')->user(),
            [
                'category_id' => (int) $validated['category_id'],
                'title' => $validated['title'],
                'found_location' => $validated['found_location'],
                'found_at' => $validated['found_at'] ?? null,
                'description' => $validated['description'] ?? null,
                'dropoff_location' => $validated['dropoff_location'] ?? null,
            ],
            $request->file('image'),
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message'      => trans('campusfind_web_web::app.web.reports.success_found_created'),
                'redirect_url' => route('campusfind_web.web.items.index'),
                'data'         => [
                    'id' => $item->id ?? null,
                    'reference' => $item->public_reference ?? null,
                ],
            ], 201);
        }

        return redirect()->back()
            ->with('success', trans('campusfind_web_web::app.web.reports.success_found_created'));
    }

    /**
     * Retrieve categories for dropdowns.
     */
    protected function getCategories(): array
    {
        if (class_exists(LostFoundCategory::class)) {
            return LostFoundCategory::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('code')
                ->get()
                ->all();
        }

        return [];
    }

    private function handleLocale(Request $request): void
    {
        $availableLocales = ['ar', 'en', 'es', 'fa', 'pt_BR', 'tr', 'vi'];

        if ($request->has('locale') && in_array($request->query('locale'), $availableLocales, true)) {
            app()->setLocale($request->query('locale'));
            session(['locale' => $request->query('locale')]);
        } elseif (session()->has('locale') && in_array(session('locale'), $availableLocales, true)) {
            app()->setLocale(session('locale'));
        }
    }
}
