<?php

namespace CampusFind\LostAndFound\Services\Application;

use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use CampusFind\LostAndFound\Enums\EvidenceType;
use CampusFind\LostAndFound\Enums\ItemStatus;
use CampusFind\LostAndFound\Models\ClaimEvidence;
use CampusFind\LostAndFound\Models\FoundItem;
use CampusFind\LostAndFound\Models\LostFoundClaim;
use CampusFind\LostAndFound\Repositories\LostFoundClaimRepository;
use CampusFind\LostAndFound\Services\ClaimEvidenceImageService;
use CampusFind\LostAndFound\Services\ClaimEvidenceService;
use CampusFind\LostAndFound\Services\ClaimResolutionService;
use CampusFind\Student\Models\Student;

class StudentClaimApplicationService
{
    public function __construct(
        protected LostFoundClaimRepository $claimRepository,
        protected ClaimEvidenceService $evidenceService,
        protected ClaimEvidenceImageService $evidenceImageService,
        protected ClaimResolutionService $claimResolutionService
    ) {}

    public function submitClaim(Student $actor, FoundItem $item, array $data = []): LostFoundClaim
    {
        if (! in_array($item->status, [ItemStatus::REPORTED, ItemStatus::IN_CUSTODY], true)) {
            throw new DomainException("Cannot submit a claim for an item in status [{$item->status->value}].");
        }

        $data['claimant_student_id'] = $actor->id;
        $data['found_item_id'] = $item->id;
        unset($data['status'], $data['withdrawn_at']);

        $statement = $data['statement'] ?? null;
        unset($data['statement']);

        return DB::transaction(function () use ($data, $statement): LostFoundClaim {
            $claim = $this->claimRepository->create($data);

            if ($statement !== null && $statement !== '') {
                $this->evidenceService->addTextEvidence($claim, EvidenceType::TEXT_DESCRIPTION, $statement);
            }

            return $claim;
        });
    }

    public function addOwnClaimEvidence(
        Student $actor,
        LostFoundClaim $claim,
        EvidenceType $type,
        string|UploadedFile $content
    ): ClaimEvidence {
        LostAndFoundAuthorization::authorizeStudentOwnership($actor, $claim->claimant_student_id, 'LostFoundClaim');

        if ($type === EvidenceType::IMAGE_ATTACHMENT) {
            return $this->evidenceImageService->store($claim, $content);
        }

        return $this->evidenceService->addTextEvidence($claim, $type, (string) $content);
    }

    public function withdrawOwnClaim(Student $actor, LostFoundClaim $claim): LostFoundClaim
    {
        LostAndFoundAuthorization::authorizeStudentOwnership($actor, $claim->claimant_student_id, 'LostFoundClaim');

        return $this->claimResolutionService->withdraw($claim->found_item_id, $claim->id);
    }
}
