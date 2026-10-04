<?php

namespace CampusFind\Web\Web\Http\Controllers;

use CampusFind\LostAndFound\Contracts\PublicLostAndFoundReadContract;
use CampusFind\LostAndFound\Enums\ReportStatus;
use CampusFind\LostAndFound\Models\FoundReportResponse;
use CampusFind\LostAndFound\Models\LostReport;
use CampusFind\LostAndFound\Services\Application\StudentFoundResponseApplicationService;
use CampusFind\LostAndFound\Services\PublicReference;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use InvalidArgumentException;

class LostReportResponseController extends Controller
{
    public function show(Request $request, string $reference): View
    {
        $this->handleLocale($request);
        $reader = app(PublicLostAndFoundReadContract::class);
        $report = $reader->findPublicLostReportByReference($reference);
        abort_unless($report, 404);

        $student = auth('student')->user();
        $source = $student ? $this->activeReport($reference) : null;
        $existingResponse = $student && $source
            ? FoundReportResponse::query()
                ->where('lost_report_id', $source->id)
                ->where('responder_student_id', $student->id)
                ->first()
            : null;
        $isOwner = $student && $source && (int) $source->student_id === (int) $student->id;

        return view('campusfind_web_web::reports.lost-detail', [
            'report' => $report,
            'student' => $student,
            'existingResponse' => $existingResponse,
            'isOwner' => $isOwner,
            'dropoffLocations' => $this->dropoffLocations(),
        ]);
    }

    public function store(Request $request, string $reference, StudentFoundResponseApplicationService $service): JsonResponse|RedirectResponse
    {
        $this->handleLocale($request);

        if (! auth('student')->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message'      => trans('campusfind_web_web::app.web.found_response.login_required'),
                    'redirect_url' => route('campusfind_web.web.login'),
                ], 401);
            }

            return redirect()->route('campusfind_web.web.login')
                ->with('error', trans('campusfind_web_web::app.web.found_response.login_required'));
        }

        $report = $this->activeReport($reference);
        abort_unless($report, 404);

        $validator = Validator::make($request->all(), [
            'found_location' => ['required', 'string', 'max:255'],
            'found_at' => ['nullable', 'date', 'before_or_equal:now'],
            'dropoff_location' => ['required', 'string', Rule::in(array_keys($this->dropoffLocations()))],
            'message' => ['nullable', 'string', 'max:2000'],
            'images' => ['nullable', 'array', 'max:3'],
            'images.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $validator->errors()->first(),
                    'errors'  => $validator->errors()->toArray(),
                ], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        try {
            $service->submit(
                auth('student')->user(),
                $report->id,
                $validator->safe()->except('images'),
                $request->file('images', []),
            );
        } catch (DomainException|InvalidArgumentException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => trans('campusfind_web_web::app.web.found_response.submission_unavailable'),
                    'errors'  => [
                        'response' => [trans('campusfind_web_web::app.web.found_response.submission_unavailable')],
                    ],
                ], 422);
            }

            return back()->withErrors([
                'response' => trans('campusfind_web_web::app.web.found_response.submission_unavailable'),
            ])->withInput();
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message'      => trans('campusfind_web_web::app.web.found_response.submitted_success'),
                'redirect_url' => route('campusfind_web.web.lost-reports.show', $report->public_reference),
            ], 200);
        }

        return redirect()->route('campusfind_web.web.lost-reports.show', $report->public_reference)
            ->with('success', trans('campusfind_web_web::app.web.found_response.submitted_success'));
    }

    public function cancel(Request $request, string $reference, StudentFoundResponseApplicationService $service): JsonResponse|RedirectResponse
    {
        $this->handleLocale($request);

        if (! auth('student')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            abort(403);
        }

        $report = $this->activeReport($reference);
        abort_unless($report, 404);

        $response = FoundReportResponse::query()
            ->where('lost_report_id', $report->id)
            ->where('responder_student_id', auth('student')->id())
            ->firstOrFail();

        try {
            $service->cancelOwn(auth('student')->user(), $response);
        } catch (DomainException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => trans('campusfind_web_web::app.web.found_response.submission_unavailable'),
                    'errors'  => [
                        'response' => [trans('campusfind_web_web::app.web.found_response.submission_unavailable')],
                    ],
                ], 422);
            }

            return back()->withErrors([
                'response' => trans('campusfind_web_web::app.web.found_response.submission_unavailable'),
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => trans('campusfind_web_web::app.web.found_response.cancelled_success'),
            ], 200);
        }

        return back()->with('success', trans('campusfind_web_web::app.web.found_response.cancelled_success'));
    }

    private function activeReport(string $reference): LostReport
    {
        return LostReport::query()
            ->where('public_reference_key', PublicReference::normalize($reference))
            ->where('status', ReportStatus::ACTIVE)
            ->firstOrFail();
    }

    /** @return array<string, string> */
    private function dropoffLocations(): array
    {
        $labels = [
            'main_security' => trans('campusfind_web_web::app.web.found_response.dropoff_main_security'),
            'student_affairs' => trans('campusfind_web_web::app.web.found_response.dropoff_student_affairs'),
            'library_desk' => trans('campusfind_web_web::app.web.found_response.dropoff_library_desk'),
        ];

        return array_intersect_key(
            $labels,
            array_flip(config('lost_found.found_responses.approved_dropoff_locations', [])),
        );
    }

    private function handleLocale(Request $request): void
    {
        $available = ['ar', 'en', 'es', 'fa', 'pt_BR', 'tr', 'vi'];
        if ($request->has('locale') && in_array($request->query('locale'), $available, true)) {
            app()->setLocale($request->query('locale'));
            session(['locale' => $request->query('locale')]);
        } elseif (session()->has('locale') && in_array(session('locale'), $available, true)) {
            app()->setLocale(session('locale'));
        }
    }
}
