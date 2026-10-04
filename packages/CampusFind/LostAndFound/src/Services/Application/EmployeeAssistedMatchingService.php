<?php

namespace CampusFind\LostAndFound\Services\Application;

use CampusFind\LostAndFound\DataTransferObjects\AssistedMatchResult;
use CampusFind\LostAndFound\Enums\ItemStatus;
use CampusFind\LostAndFound\Enums\MatchReviewDecision;
use CampusFind\LostAndFound\Enums\ReportStatus;
use CampusFind\LostAndFound\Models\FoundItem;
use CampusFind\LostAndFound\Models\LostReport;
use CampusFind\LostAndFound\Models\MatchSuggestionReview;
use CampusFind\LostAndFound\Models\MatchSuggestionSnapshot;
use CampusFind\LostAndFound\Models\PotentialReportItemMatch;
use CampusFind\LostAndFound\Services\Matching\AssistedMatchScorer;
use CampusFind\LostAndFound\Services\Matching\MatchCandidateRetriever;
use CampusFind\LostAndFound\Services\SecurityInvariants;
use DomainException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Webkul\User\Models\User;

class EmployeeAssistedMatchingService
{
    public function __construct(
        private MatchCandidateRetriever $retriever,
        private AssistedMatchScorer $scorer,
    ) {}

    /** @return array{evaluated: int, created: int, refreshed: int, unchanged: int, rejected: int} */
    public function generateForLostReport(User $actor, int $lostReportId): array
    {
        LostAndFoundAuthorization::authorizeUser($actor, 'lost_found.matches.generate');

        return DB::transaction(function () use ($actor, $lostReportId): array {
            $lockedActor = $this->lockAndAuthorizeActor($actor->getKey(), 'lost_found.matches.generate');
            $report = LostReport::query()->lockForUpdate()->findOrFail($lostReportId);
            $this->assertReportEligible($report);
            $items = $this->retriever->forLostReport($report);
            $results = $items->map(fn (FoundItem $item): AssistedMatchResult => $this->scorer->score($report, $item));

            return $this->persistRankedResults($lockedActor, $results);
        });
    }

    /** @return array{evaluated: int, created: int, refreshed: int, unchanged: int, rejected: int} */
    public function generateForFoundItem(User $actor, int $foundItemId): array
    {
        LostAndFoundAuthorization::authorizeUser($actor, 'lost_found.matches.generate');

        return DB::transaction(function () use ($actor, $foundItemId): array {
            $lockedActor = $this->lockAndAuthorizeActor($actor->getKey(), 'lost_found.matches.generate');
            $item = FoundItem::query()->with('privateDetail')->lockForUpdate()->findOrFail($foundItemId);
            $this->assertItemEligible($item);
            $reports = $this->retriever->forFoundItem($item);
            $results = $reports->map(fn (LostReport $report): AssistedMatchResult => $this->scorer->score($report, $item));

            return $this->persistRankedResults($lockedActor, $results);
        });
    }

    public function review(
        User $actor,
        int $potentialMatchId,
        MatchReviewDecision $decision,
        ?string $notes = null,
    ): MatchSuggestionReview {
        LostAndFoundAuthorization::authorizeUser($actor, 'lost_found.matches.review');
        $notes = trim((string) $notes);

        if ($decision === MatchReviewDecision::REJECTED && $notes === '') {
            throw new InvalidArgumentException('A rejection reason is required.');
        }

        if ($notes !== '') {
            SecurityInvariants::assertNoAuthSecrets($notes);
        }

        return DB::transaction(function () use ($actor, $potentialMatchId, $decision, $notes): MatchSuggestionReview {
            $match = PotentialReportItemMatch::query()->lockForUpdate()->findOrFail($potentialMatchId);
            $lockedActor = $this->lockAndAuthorizeActor($actor->getKey(), 'lost_found.matches.review');

            if ($match->verifiedLink()->lockForUpdate()->exists()) {
                throw new DomainException('A verified relationship cannot be reviewed as a suggestion.');
            }

            $latest = $match->reviews()->latest('id')->lockForUpdate()->first();

            if ($latest?->decision === MatchReviewDecision::REJECTED) {
                throw new DomainException('A rejected suggestion is terminal and cannot be reopened automatically.');
            }

            return MatchSuggestionReview::query()->create([
                'potential_match_id' => $match->getKey(),
                'reviewer_user_id' => $lockedActor->getKey(),
                'decision' => $decision,
                'notes' => $notes !== '' ? $notes : null,
                'reviewed_at' => now(),
            ])->refresh();
        });
    }

    /**
     * @param  Collection<int, AssistedMatchResult>  $results
     * @return array{evaluated: int, created: int, refreshed: int, unchanged: int, rejected: int}
     */
    private function persistRankedResults(User $actor, Collection $results): array
    {
        $resultLimit = max(1, min(100, (int) config('lost_found.matching.result_limit', 25)));
        $ranked = $results
            ->sort(static fn (AssistedMatchResult $left, AssistedMatchResult $right): int => [
                -$left->scoreBasisPoints,
                $left->foundItemId,
                $left->lostReportId,
            ] <=> [
                -$right->scoreBasisPoints,
                $right->foundItemId,
                $right->lostReportId,
            ])
            ->take($resultLimit)
            ->values();

        $stats = ['evaluated' => $results->count(), 'created' => 0, 'refreshed' => 0, 'unchanged' => 0, 'rejected' => 0];

        if ($ranked->isEmpty()) {
            return $stats;
        }

        $existing = PotentialReportItemMatch::query()
            ->with(['latestReview', 'latestSnapshot', 'verifiedLink', 'snapshots:id,potential_match_id,input_fingerprint'])
            ->where(function ($query) use ($ranked): void {
                foreach ($ranked as $result) {
                    $query->orWhere(function ($pair) use ($result): void {
                        $pair->where('lost_report_id', $result->lostReportId)
                            ->where('found_item_id', $result->foundItemId);
                    });
                }
            })
            ->lockForUpdate()
            ->get()
            ->keyBy(static fn (PotentialReportItemMatch $match): string => $match->lost_report_id.':'.$match->found_item_id);

        foreach ($ranked as $result) {
            $key = $result->lostReportId.':'.$result->foundItemId;
            $match = $existing->get($key);

            if ($match?->verifiedLink !== null) {
                $stats['unchanged']++;

                continue;
            }

            if ($match?->latestReview?->decision === MatchReviewDecision::REJECTED) {
                $stats['rejected']++;

                continue;
            }

            if ($match === null) {
                $match = PotentialReportItemMatch::query()->create([
                    'lost_report_id' => $result->lostReportId,
                    'found_item_id' => $result->foundItemId,
                    'proposed_by_user_id' => $actor->getKey(),
                    'proposed_at' => now(),
                ]);
                $stats['created']++;
            } elseif ($match->latestSnapshot?->input_fingerprint === $result->inputFingerprint) {
                $stats['unchanged']++;

                continue;
            } elseif ($match->snapshots->contains('input_fingerprint', $result->inputFingerprint)) {
                $stats['unchanged']++;

                continue;
            } else {
                $stats['refreshed']++;
            }

            MatchSuggestionSnapshot::query()->create([
                'potential_match_id' => $match->getKey(),
                'generated_by_user_id' => $actor->getKey(),
                'algorithm_version' => $result->algorithmVersion,
                'score_basis_points' => $result->scoreBasisPoints,
                'signals' => $result->signals,
                'input_fingerprint' => $result->inputFingerprint,
                'generated_at' => now(),
            ]);
        }

        return $stats;
    }

    private function lockAndAuthorizeActor(int $actorId, string $permission): User
    {
        $actor = User::query()->with('role')->lockForUpdate()->find($actorId);

        if (! $actor || ! (bool) $actor->status) {
            throw new DomainException('Assisted matching requires an active staff user.');
        }

        LostAndFoundAuthorization::authorizeUser($actor, $permission);

        return $actor;
    }

    private function assertReportEligible(LostReport $report): void
    {
        if ($report->status !== ReportStatus::ACTIVE || $report->resolved_found_item_id !== null) {
            throw new DomainException('Only an unresolved active lost report can be matched.');
        }
    }

    private function assertItemEligible(FoundItem $item): void
    {
        if (! in_array($item->status, [ItemStatus::REPORTED, ItemStatus::IN_CUSTODY], true)) {
            throw new DomainException('Only an available found item can be matched.');
        }
    }
}
