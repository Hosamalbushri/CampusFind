<?php

namespace CampusFind\Web\Web\Http\Controllers;

use CampusFind\LostAndFound\Contracts\PublicLostAndFoundReadContract;
use CampusFind\LostAndFound\DataTransferObjects\PublicUnifiedSearchCriteria;
use CampusFind\LostAndFound\DataTransferObjects\PublicUnifiedSearchResult;
use CampusFind\LostAndFound\Enums\EvidenceType;
use CampusFind\LostAndFound\Models\FoundItem;
use CampusFind\LostAndFound\Models\LostFoundClaim;
use CampusFind\LostAndFound\Services\Application\StudentClaimApplicationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ItemController extends Controller
{
    public function index(Request $request): View
    {
        $this->handleLocale($request);

        $validator = Validator::make($request->query(), [
            'query' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', 'string', 'in:all,lost,found'],
            'category' => ['nullable', 'string', 'max:64'],
            'location' => ['nullable', 'string', 'max:100'],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
            'status' => ['nullable', 'string', 'in:active,reported,in_custody'],
            'sort' => ['nullable', 'string', 'in:newest,oldest,title_asc,title_desc'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $filters = [
            'query' => $this->stringQuery($request, 'query'),
            'type' => $this->stringQuery($request, 'type') ?? 'all',
            'category' => $this->stringQuery($request, 'category'),
            'location' => $this->stringQuery($request, 'location'),
            'date_from' => $this->stringQuery($request, 'date_from'),
            'date_to' => $this->stringQuery($request, 'date_to'),
            'status' => $this->stringQuery($request, 'status'),
            'sort' => $this->stringQuery($request, 'sort') ?? 'newest',
        ];
        $page = filter_var($request->query('page', 1), FILTER_VALIDATE_INT);

        $reader = app()->bound(PublicLostAndFoundReadContract::class)
            ? app()->make(PublicLostAndFoundReadContract::class)
            : null;

        if ($reader !== null) {
            $criteria = new PublicUnifiedSearchCriteria(
                query: $filters['query'],
                type: $filters['type'],
                category: $filters['category'],
                location: $filters['location'],
                dateFrom: $filters['date_from'],
                dateTo: $filters['date_to'],
                status: $filters['status'],
                sort: $filters['sort'],
                page: is_int($page) ? $page : 1,
                perPage: 12,
            );

            $searchResult = $reader->searchPublicRecords($criteria);
            $categories = $reader->getPublicCategories();
        } else {
            $searchResult = new PublicUnifiedSearchResult(
                items: [],
                total: 0,
                perPage: 12,
                currentPage: 1,
                lastPage: 1,
            );
            $categories = [];
        }

        return view('campusfind_web_web::items.index', [
            'searchResult' => $searchResult,
            'categories' => $categories,
            'filters' => $filters,
            'hasFilterErrors' => $validator->fails(),
        ]);
    }

    public function show(Request $request, string $reference): View
    {
        $this->handleLocale($request);

        if (! app()->bound(PublicLostAndFoundReadContract::class)) {
            throw new NotFoundHttpException('Lost and Found service is unavailable.');
        }

        /** @var PublicLostAndFoundReadContract $reader */
        $reader = app()->make(PublicLostAndFoundReadContract::class);
        $item = $reader->findPublicFoundItemByReference($reference);

        if ($item === null) {
            abort(404, 'Item not found');
        }

        $isStudent = auth()->guard('student')->check();
        $student = $isStudent ? auth()->guard('student')->user() : null;
        $existingClaim = null;
        $foundItemId = null;

        if (class_exists(FoundItem::class)) {
            $foundItem = FoundItem::where('public_reference', $reference)->first();
            if ($foundItem) {
                $foundItemId = $foundItem->id;
                if ($student && class_exists(LostFoundClaim::class)) {
                    $existingClaim = LostFoundClaim::where('found_item_id', $foundItem->id)
                        ->where('claimant_student_id', $student->id)
                        ->first();
                }
            }
        }

        return view('campusfind_web_web::items.show', [
            'item' => $item,
            'isStudent' => $isStudent,
            'student' => $student,
            'existingClaim' => $existingClaim,
            'foundItemId' => $foundItemId,
        ]);
    }

    /**
     * Submit a claim for a found item by an authenticated student.
     */
    public function storeClaim(Request $request, string $reference): JsonResponse|RedirectResponse
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

        $student = auth()->guard('student')->user();

        $validated = $request->validate([
            'statement' => ['required', 'string', 'max:2000'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,webp,jpg', 'max:5120'],
        ]);

        if (! class_exists(FoundItem::class)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Item claiming is currently unavailable.',
                ], 503);
            }

            return redirect()->back()->with('error', 'Item claiming is currently unavailable.');
        }

        $foundItem = FoundItem::where('public_reference', $reference)->firstOrFail();

        // Check if student already claimed this item
        if (class_exists(LostFoundClaim::class)) {
            $existing = LostFoundClaim::where('found_item_id', $foundItem->id)
                ->where('claimant_student_id', $student->id)
                ->first();

            if ($existing) {
                $duplicateMsg = trans('lost_found::app.student.claims.duplicate_error') ?? 'You already submitted a claim for this item.';

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => $duplicateMsg,
                        'errors'  => [
                            'statement' => [$duplicateMsg],
                        ],
                    ], 422);
                }

                return redirect()->back()->with('error', $duplicateMsg);
            }
        }

        if (class_exists(StudentClaimApplicationService::class)) {
            /** @var StudentClaimApplicationService $claimService */
            $claimService = app(StudentClaimApplicationService::class);
            $claim = $claimService->submitClaim($student, $foundItem, ['statement' => $validated['statement']]);

            if ($request->hasFile('image')) {
                try {
                    $claimService->addOwnClaimEvidence(
                        $student,
                        $claim,
                        EvidenceType::IMAGE_ATTACHMENT,
                        $request->file('image')
                    );
                } catch (\Throwable $e) {
                    Log::warning('Claim evidence image upload failed: '.$e->getMessage());
                }
            }
        }

        $successMsg = trans('lost_found::app.student.claims.submitted_success') ?? 'Claim submitted successfully.';

        if ($request->expectsJson()) {
            return response()->json([
                'message'      => $successMsg,
                'redirect_url' => route('campusfind_web.web.account.dashboard'),
            ], 201);
        }

        return redirect()->route('campusfind_web.web.account.dashboard')
            ->with('success', $successMsg);
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

    private function stringQuery(Request $request, string $key): ?string
    {
        $value = $request->query($key);

        return is_string($value) ? $value : null;
    }
}
