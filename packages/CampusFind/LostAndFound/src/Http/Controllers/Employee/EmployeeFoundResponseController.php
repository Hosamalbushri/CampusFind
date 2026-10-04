<?php

namespace CampusFind\LostAndFound\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use CampusFind\LostAndFound\Http\Requests\Employee\RejectFoundResponseRequest;
use CampusFind\LostAndFound\Http\Requests\Employee\ReviewFoundResponseRequest;
use CampusFind\LostAndFound\Http\Requests\Employee\VerifyFoundResponseRequest;
use CampusFind\LostAndFound\Models\FoundReportResponse;
use CampusFind\LostAndFound\Models\FoundReportResponseImage;
use CampusFind\LostAndFound\Services\Application\EmployeeFoundResponseApplicationService;
use CampusFind\LostAndFound\Services\Application\LostAndFoundAuthorization;
use CampusFind\LostAndFound\Services\FoundResponseImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class EmployeeFoundResponseController extends Controller
{
    public function __construct(private EmployeeFoundResponseApplicationService $service) {}

    public function index(): View
    {
        $this->authorizeRead();
        $responses = FoundReportResponse::query()
            ->with(['lostReport:id,public_reference,title', 'responder:id,name', 'reviewer:id,name'])
            ->latest('submitted_at')
            ->paginate(25);

        return view('lost_found::employee.responses.index', compact('responses'));
    }

    public function show(int $id): View
    {
        $this->authorizeRead();
        $response = FoundReportResponse::query()
            ->with([
                'lostReport:id,public_reference,title,status',
                'responder:id,name,university_card_number',
                'reviewer:id,name',
                'resultingFoundItem:id,public_reference,title,status',
                'images:id,response_id,mime_type,byte_size,submitted_at',
                'reviews.reviewer:id,name',
            ])
            ->findOrFail($id);

        return view('lost_found::employee.responses.show', compact('response'));
    }

    public function image(int $id, int $imageId): Response
    {
        $this->authorizeRead();
        $image = FoundReportResponseImage::query()
            ->where('response_id', $id)
            ->where('id', $imageId)
            ->firstOrFail();
        $disk = Storage::disk(app(FoundResponseImageService::class)->diskName());
        abort_unless($disk->exists($image->storage_key), 404);

        return $disk->response($image->storage_key, 'found-response-image', [
            'Content-Type' => $image->mime_type,
            'Content-Disposition' => 'inline',
        ]);
    }

    public function review(ReviewFoundResponseRequest $request, int $id): JsonResponse|RedirectResponse
    {
        $updated = $this->service->startReview(
            auth('user')->user(),
            FoundReportResponse::findOrFail($id),
            $request->validated('notes'),
        );

        return $this->success($request, $updated, 'lost_found::app.admin.responses.reviewed_success');
    }

    public function reject(RejectFoundResponseRequest $request, int $id): JsonResponse|RedirectResponse
    {
        $updated = $this->service->reject(
            auth('user')->user(),
            FoundReportResponse::findOrFail($id),
            $request->validated('notes'),
        );

        return $this->success($request, $updated, 'lost_found::app.admin.responses.rejected_success');
    }

    public function verify(VerifyFoundResponseRequest $request, int $id): JsonResponse|RedirectResponse
    {
        $updated = $this->service->verify(
            auth('user')->user(),
            FoundReportResponse::findOrFail($id),
            $request->validated('found_item_reference'),
            $request->validated('verification_evidence'),
        );

        return $this->success($request, $updated, 'lost_found::app.admin.responses.verified_success');
    }

    private function authorizeRead(): void
    {
        LostAndFoundAuthorization::authorizeUser(auth('user')->user(), 'lost_found.responses.view');
    }

    private function success($request, FoundReportResponse $response, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => trans($message),
                'data' => ['reference' => $response->public_reference, 'status' => $response->status->value],
            ]);
        }

        return redirect()->route('admin.lost_found.responses.show', $response->id)->with('success', trans($message));
    }
}
