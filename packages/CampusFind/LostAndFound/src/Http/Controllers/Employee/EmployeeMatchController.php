<?php

namespace CampusFind\LostAndFound\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use CampusFind\LostAndFound\Enums\MatchReviewDecision;
use CampusFind\LostAndFound\Models\MatchSuggestionSnapshot;
use CampusFind\LostAndFound\Models\PotentialReportItemMatch;
use CampusFind\LostAndFound\Services\Application\EmployeeAssistedMatchingService;
use CampusFind\LostAndFound\Services\Application\EmployeeReportItemLinkApplicationService;
use CampusFind\LostAndFound\Services\Application\LostAndFoundAuthorization;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeMatchController extends Controller
{
    public function index(Request $request): View
    {
        $actor = auth('user')->user();
        LostAndFoundAuthorization::authorizeUser($actor, 'lost_found.matches.view');
        $filters = $request->validate([
            'lost_report_id' => ['nullable', 'integer', 'min:1'],
            'found_item_id' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', Rule::in(['suggested', 'reviewed', 'rejected', 'verified'])],
            'sort' => ['nullable', Rule::in(['score', 'newest', 'oldest'])],
        ]);

        $scoreSubquery = MatchSuggestionSnapshot::query()
            ->select('score_basis_points')
            ->whereColumn('potential_match_id', 'lost_found_potential_matches.id')
            ->latest('id')
            ->limit(1);

        $query = PotentialReportItemMatch::query()
            ->select('lost_found_potential_matches.*')
            ->selectSub($scoreSubquery, 'latest_score_basis_points')
            ->with([
                'lostReport:id,public_reference,title,public_description,lost_location,lost_at,status',
                'foundItem:id,public_reference,title,public_description,found_location,found_at,status',
                'latestSnapshot',
                'latestReview',
                'verifiedLink:id,potential_match_id',
            ])
            ->when($filters['lost_report_id'] ?? null, fn ($builder, $id) => $builder->where('lost_report_id', $id))
            ->when($filters['found_item_id'] ?? null, fn ($builder, $id) => $builder->where('found_item_id', $id));

        $this->applyStatusFilter($query, $filters['status'] ?? null);

        match ($filters['sort'] ?? 'score') {
            'newest' => $query->orderByDesc('proposed_at')->orderBy('id'),
            'oldest' => $query->orderBy('proposed_at')->orderBy('id'),
            default => $query->orderByDesc('latest_score_basis_points')->orderBy('id'),
        };

        return view('lost_found::employee.matches.index', [
            'matches' => $query->paginate(25)->withQueryString(),
            'filters' => $filters,
            'canGenerate' => $this->can($actor, 'lost_found.matches.generate'),
            'canReview' => $this->can($actor, 'lost_found.matches.review'),
            'canVerify' => $this->can($actor, 'lost_found.items.edit'),
        ]);
    }

    public function generateForReport(int $id, EmployeeAssistedMatchingService $service): RedirectResponse
    {
        $stats = $service->generateForLostReport(auth('user')->user(), $id);

        return back()->with('success', trans('lost_found::app.admin.matches.generated_success', $stats));
    }

    public function generateForItem(int $id, EmployeeAssistedMatchingService $service): RedirectResponse
    {
        $stats = $service->generateForFoundItem(auth('user')->user(), $id);

        return back()->with('success', trans('lost_found::app.admin.matches.generated_success', $stats));
    }

    public function review(Request $request, int $id, EmployeeAssistedMatchingService $service): RedirectResponse
    {
        $validated = $request->validate([
            'decision' => ['required', Rule::enum(MatchReviewDecision::class)],
            'notes' => ['nullable', 'string', 'max:2000', 'required_if:decision,rejected'],
        ]);

        $service->review(
            auth('user')->user(),
            $id,
            MatchReviewDecision::from($validated['decision']),
            $validated['notes'] ?? null,
        );

        return back()->with('success', trans('lost_found::app.admin.matches.reviewed_success'));
    }

    public function verify(Request $request, int $id, EmployeeReportItemLinkApplicationService $service): RedirectResponse
    {
        $actor = auth('user')->user();
        LostAndFoundAuthorization::authorizeUser($actor, 'lost_found.matches.view');
        $validated = $request->validate([
            'verification_evidence' => ['required', 'string', 'max:4000'],
        ]);
        $service->verifyPotentialMatch($actor, $id, $validated['verification_evidence']);

        return back()->with('success', trans('lost_found::app.admin.matches.verified_success'));
    }

    private function applyStatusFilter($query, ?string $status): void
    {
        match ($status) {
            'verified' => $query->whereHas('verifiedLink'),
            'rejected' => $query->whereDoesntHave('verifiedLink')->whereHas(
                'latestReview',
                fn ($review) => $review->where('decision', MatchReviewDecision::REJECTED->value),
            ),
            'reviewed' => $query->whereDoesntHave('verifiedLink')->whereHas(
                'latestReview',
                fn ($review) => $review->where('decision', MatchReviewDecision::REVIEWED->value),
            ),
            'suggested' => $query->whereDoesntHave('verifiedLink')->whereDoesntHave('latestReview'),
            default => null,
        };
    }

    private function can($actor, string $permission): bool
    {
        return $actor->role?->permission_type === 'all' || $actor->hasPermission($permission);
    }
}
