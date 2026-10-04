<?php

namespace CampusFind\LostAndFound\Services\Application;

use CampusFind\LostAndFound\Enums\ItemStatus;
use CampusFind\LostAndFound\Enums\MatchReviewDecision;
use CampusFind\LostAndFound\Enums\ReportStatus;
use CampusFind\LostAndFound\Models\FoundItem;
use CampusFind\LostAndFound\Models\LostReport;
use CampusFind\LostAndFound\Models\PotentialReportItemMatch;
use CampusFind\LostAndFound\Models\VerifiedReportItemLink;
use CampusFind\LostAndFound\Services\SecurityInvariants;
use DomainException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Webkul\User\Models\User;

class EmployeeReportItemLinkApplicationService
{
    public function verifyReportItemPair(
        User $actor,
        int $lostReportId,
        int $foundItemId,
        string $verificationEvidence,
    ): VerifiedReportItemLink {
        LostAndFoundAuthorization::authorizeUser($actor, 'lost_found.items.edit');
        $verificationEvidence = trim($verificationEvidence);

        if ($verificationEvidence === '') {
            throw new InvalidArgumentException('Verification evidence is required.');
        }

        SecurityInvariants::assertNoAuthSecrets($verificationEvidence);

        return DB::transaction(function () use ($actor, $lostReportId, $foundItemId, $verificationEvidence): VerifiedReportItemLink {
            $item = FoundItem::query()->lockForUpdate()->findOrFail($foundItemId);
            $lockedActor = $this->lockAndAuthorizeActor($actor->getKey());
            $report = LostReport::query()->lockForUpdate()->findOrFail($lostReportId);
            $this->assertLinkableStates($report, $item);

            $existing = VerifiedReportItemLink::query()
                ->where('lost_report_id', $report->getKey())
                ->orWhere('found_item_id', $item->getKey())
                ->lockForUpdate()
                ->first();

            if ($existing) {
                if ((int) $existing->lost_report_id === (int) $report->getKey()
                    && (int) $existing->found_item_id === (int) $item->getKey()) {
                    return $existing;
                }

                throw new DomainException('The report or item conflicts with an existing verified relationship.');
            }

            $potential = PotentialReportItemMatch::query()
                ->where('lost_report_id', $report->getKey())
                ->where('found_item_id', $item->getKey())
                ->lockForUpdate()
                ->first();

            if (! $potential) {
                $potential = PotentialReportItemMatch::query()->create([
                    'lost_report_id' => $report->getKey(),
                    'found_item_id' => $item->getKey(),
                    'proposed_by_user_id' => $lockedActor->getKey(),
                    'proposed_at' => now(),
                ]);
            } elseif ($potential->latestReview()->where('decision', MatchReviewDecision::REJECTED->value)->exists()) {
                throw new DomainException('A rejected assisted-match suggestion cannot be verified.');
            }

            return VerifiedReportItemLink::query()->create([
                'potential_match_id' => $potential->getKey(),
                'lost_report_id' => $report->getKey(),
                'found_item_id' => $item->getKey(),
                'verified_by_user_id' => $lockedActor->getKey(),
                'verification_evidence' => $verificationEvidence,
                'verified_at' => now(),
            ])->refresh();
        });
    }

    public function proposePotentialMatch(
        User $actor,
        int $lostReportId,
        int $foundItemId,
    ): PotentialReportItemMatch {
        LostAndFoundAuthorization::authorizeUser($actor, 'lost_found.items.edit');

        return DB::transaction(function () use ($actor, $lostReportId, $foundItemId): PotentialReportItemMatch {
            $item = FoundItem::query()->lockForUpdate()->findOrFail($foundItemId);
            $lockedActor = $this->lockAndAuthorizeActor($actor->getKey());
            $report = LostReport::query()->lockForUpdate()->findOrFail($lostReportId);

            $this->assertLinkableStates($report, $item);

            if (PotentialReportItemMatch::query()
                ->where('lost_report_id', $report->getKey())
                ->where('found_item_id', $item->getKey())
                ->lockForUpdate()
                ->exists()) {
                throw new DomainException('A potential match already exists for this lost report and found item.');
            }

            return PotentialReportItemMatch::query()->create([
                'lost_report_id' => $report->getKey(),
                'found_item_id' => $item->getKey(),
                'proposed_by_user_id' => $lockedActor->getKey(),
                'proposed_at' => now(),
            ])->refresh();
        });
    }

    public function verifyPotentialMatch(
        User $actor,
        int $potentialMatchId,
        string $verificationEvidence,
    ): VerifiedReportItemLink {
        LostAndFoundAuthorization::authorizeUser($actor, 'lost_found.items.edit');

        $verificationEvidence = trim($verificationEvidence);

        if ($verificationEvidence === '') {
            throw new InvalidArgumentException('Verification evidence is required.');
        }

        SecurityInvariants::assertNoAuthSecrets($verificationEvidence);

        return DB::transaction(function () use (
            $actor,
            $potentialMatchId,
            $verificationEvidence,
        ): VerifiedReportItemLink {
            $potentialMatch = PotentialReportItemMatch::query()
                ->lockForUpdate()
                ->findOrFail($potentialMatchId);
            $item = FoundItem::query()->lockForUpdate()->findOrFail($potentialMatch->found_item_id);
            $lockedActor = $this->lockAndAuthorizeActor($actor->getKey());
            $report = LostReport::query()->lockForUpdate()->findOrFail($potentialMatch->lost_report_id);

            $this->assertLinkableStates($report, $item);

            if ($potentialMatch->latestReview()->where('decision', MatchReviewDecision::REJECTED->value)->exists()) {
                throw new DomainException('A rejected assisted-match suggestion cannot be verified.');
            }

            if (VerifiedReportItemLink::query()
                ->where('potential_match_id', $potentialMatch->getKey())
                ->orWhere('lost_report_id', $report->getKey())
                ->orWhere('found_item_id', $item->getKey())
                ->lockForUpdate()
                ->exists()) {
                throw new DomainException('The potential match conflicts with an existing verified relationship.');
            }

            return VerifiedReportItemLink::query()->create([
                'potential_match_id' => $potentialMatch->getKey(),
                'lost_report_id' => $report->getKey(),
                'found_item_id' => $item->getKey(),
                'verified_by_user_id' => $lockedActor->getKey(),
                'verification_evidence' => $verificationEvidence,
                'verified_at' => now(),
            ])->refresh();
        });
    }

    private function lockAndAuthorizeActor(int $actorId): User
    {
        $actor = User::query()->with('role')->lockForUpdate()->find($actorId);

        if (! $actor || ! (bool) $actor->status) {
            throw new DomainException('Relationship verification requires an active staff user.');
        }

        LostAndFoundAuthorization::authorizeUser($actor, 'lost_found.items.edit');

        return $actor;
    }

    private function assertLinkableStates(LostReport $report, FoundItem $item): void
    {
        if ($report->status !== ReportStatus::ACTIVE || $report->resolved_found_item_id !== null) {
            throw new DomainException('Only an unresolved active lost report can be linked.');
        }

        if (! in_array($item->status, [ItemStatus::REPORTED, ItemStatus::IN_CUSTODY], true)) {
            throw new DomainException('Only an available found item can be linked.');
        }
    }
}
