<?php

namespace CampusFind\LostAndFound\Services\Application;

use CampusFind\LostAndFound\Enums\FoundResponseStatus;
use CampusFind\LostAndFound\Models\FoundItem;
use CampusFind\LostAndFound\Models\FoundReportResponse;
use CampusFind\LostAndFound\Services\FoundResponseStateService;
use CampusFind\LostAndFound\Services\PublicReference;
use DomainException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Webkul\User\Models\User;

class EmployeeFoundResponseApplicationService
{
    public function __construct(private EmployeeReportItemLinkApplicationService $linkService) {}

    public function startReview(User $actor, FoundReportResponse $response, ?string $notes = null): FoundReportResponse
    {
        LostAndFoundAuthorization::authorizeUser($actor, 'lost_found.responses.review');

        return $this->transition($actor, $response, FoundResponseStatus::UNDER_REVIEW, $notes);
    }

    public function reject(User $actor, FoundReportResponse $response, string $notes): FoundReportResponse
    {
        LostAndFoundAuthorization::authorizeUser($actor, 'lost_found.responses.review');

        if (trim($notes) === '') {
            throw new InvalidArgumentException('A rejection reason is required.');
        }

        return $this->transition($actor, $response, FoundResponseStatus::REJECTED, $notes);
    }

    public function verify(
        User $actor,
        FoundReportResponse $response,
        string $foundItemReference,
        string $verificationEvidence,
    ): FoundReportResponse {
        LostAndFoundAuthorization::authorizeUser($actor, 'lost_found.responses.verify');
        LostAndFoundAuthorization::authorizeUser($actor, 'lost_found.items.edit');

        $foundItemKey = PublicReference::normalize($foundItemReference);
        $verificationEvidence = trim($verificationEvidence);

        if ($verificationEvidence === '') {
            throw new InvalidArgumentException('Verification evidence is required.');
        }

        return DB::transaction(function () use ($actor, $response, $foundItemKey, $verificationEvidence): FoundReportResponse {
            $locked = FoundReportResponse::query()->lockForUpdate()->findOrFail($response->getKey());
            $lockedActor = User::query()->with('role')->lockForUpdate()->find($actor->getKey());

            if (! $lockedActor || ! (bool) $lockedActor->status) {
                throw new DomainException('Found-response verification requires an active employee.');
            }

            LostAndFoundAuthorization::authorizeUser($lockedActor, 'lost_found.responses.verify');
            LostAndFoundAuthorization::authorizeUser($lockedActor, 'lost_found.items.edit');

            if ($locked->status !== FoundResponseStatus::UNDER_REVIEW) {
                throw new DomainException('Only a response under review can be verified.');
            }

            $item = FoundItem::query()->where('public_reference_key', $foundItemKey)->firstOrFail();
            $this->linkService->verifyReportItemPair(
                $lockedActor,
                $locked->lost_report_id,
                $item->getKey(),
                $verificationEvidence,
            );

            $from = $locked->status;
            $locked->status = FoundResponseStateService::transition($from, FoundResponseStatus::VERIFIED);
            $locked->reviewer_user_id = $lockedActor->getKey();
            $locked->resulting_found_item_id = $item->getKey();
            $locked->verified_at = now();
            $locked->save();
            $this->appendReview($locked, $lockedActor, $from, FoundResponseStatus::VERIFIED, $verificationEvidence);

            return $locked->refresh();
        });
    }

    private function transition(
        User $actor,
        FoundReportResponse $response,
        FoundResponseStatus $to,
        ?string $notes,
    ): FoundReportResponse {
        return DB::transaction(function () use ($actor, $response, $to, $notes): FoundReportResponse {
            $locked = FoundReportResponse::query()->lockForUpdate()->findOrFail($response->getKey());
            $lockedActor = User::query()->with('role')->lockForUpdate()->find($actor->getKey());

            if (! $lockedActor || ! (bool) $lockedActor->status) {
                throw new DomainException('Found-response review requires an active employee.');
            }

            LostAndFoundAuthorization::authorizeUser($lockedActor, 'lost_found.responses.review');
            $from = $locked->status;
            $locked->status = FoundResponseStateService::transition($from, $to);
            $locked->reviewer_user_id = $lockedActor->getKey();

            if ($to === FoundResponseStatus::UNDER_REVIEW) {
                $locked->review_started_at = now();
            } elseif ($to === FoundResponseStatus::REJECTED) {
                $locked->rejected_at = now();
            }

            $locked->save();
            $this->appendReview($locked, $lockedActor, $from, $to, $notes);

            return $locked->refresh();
        });
    }

    private function appendReview(
        FoundReportResponse $response,
        User $actor,
        FoundResponseStatus $from,
        FoundResponseStatus $to,
        ?string $notes,
    ): void {
        $response->reviews()->create([
            'reviewer_user_id' => $actor->getKey(),
            'from_status' => $from,
            'to_status' => $to,
            'staff_notes' => $notes,
            'reviewed_at' => now(),
        ]);
    }
}
