<?php

namespace CampusFind\LostAndFound\Services\Application;

use CampusFind\LostAndFound\Enums\FoundItemImageVisibility;
use CampusFind\LostAndFound\Enums\FoundItemSubmissionChannel;
use CampusFind\LostAndFound\Enums\ItemStatus;
use CampusFind\LostAndFound\Models\FoundItem;
use CampusFind\LostAndFound\Models\FoundItemImage;
use CampusFind\LostAndFound\Repositories\FoundItemRepository;
use CampusFind\LostAndFound\Services\FoundItemImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Webkul\User\Models\User;

class EmployeeItemApplicationService
{
    public function __construct(
        protected FoundItemRepository $itemRepository,
        protected FoundItemImageService $imageService
    ) {}

    public function createFoundItem(User $actor, array $data): FoundItem
    {
        LostAndFoundAuthorization::authorizeUser($actor, 'lost_found.items.create');

        $data['logged_by_user_id'] = $actor->id;
        $data['submission_channel'] = FoundItemSubmissionChannel::EMPLOYEE_ASSISTED;
        $data['reporter_student_id'] = $data['reporter_student_id'] ?? null;
        $data['submitted_by_student_id'] = null;
        $data['intake_employee_user_id'] = $actor->id;
        $data['public_reference'] = $data['public_reference'] ?? 'FI-'.strtoupper(Str::random(10));
        $data['status'] = $data['status'] ?? ItemStatus::REPORTED->value;

        if (isset($data['description'])) {
            $data['public_description'] = $data['description'];
            unset($data['description']);
        }

        $privateDetails = [
            'identifying_details' => $data['identifying_details'] ?? $data['distinguishing_marks'] ?? null,
            'serial_fragment' => $data['serial_fragment'] ?? null,
            'staff_notes' => $data['staff_notes'] ?? null,
        ];
        unset(
            $data['approved_claim_id'],
            $data['identifying_details'],
            $data['distinguishing_marks'],
            $data['serial_fragment'],
            $data['staff_notes'],
            $data['storage_location']
        );

        return DB::transaction(function () use ($data, $privateDetails): FoundItem {
            $item = $this->itemRepository->create($data);

            if (array_filter($privateDetails, static fn ($v): bool => $v !== null && $v !== '') !== []) {
                $item->privateDetail()->create($privateDetails);
            }

            return $item->refresh();
        });
    }

    public function updateFoundItem(User $actor, int $id, array $data): FoundItem
    {
        LostAndFoundAuthorization::authorizeUser($actor, 'lost_found.items.edit');

        if (isset($data['description'])) {
            $data['public_description'] = $data['description'];
            unset($data['description']);
        }

        $privateDetails = [
            'identifying_details' => $data['identifying_details'] ?? $data['distinguishing_marks'] ?? null,
            'serial_fragment' => $data['serial_fragment'] ?? null,
            'staff_notes' => $data['staff_notes'] ?? null,
        ];

        unset(
            $data['identifying_details'],
            $data['distinguishing_marks'],
            $data['serial_fragment'],
            $data['staff_notes'],
            $data['storage_location']
        );

        $item = $this->itemRepository->update($data, $id);

        if (array_filter($privateDetails, static fn ($v): bool => $v !== null && $v !== '') !== []) {
            if ($item->privateDetail) {
                $item->privateDetail->update(array_filter($privateDetails, static fn ($v): bool => $v !== null));
            } else {
                $item->privateDetail()->create($privateDetails);
            }
        }

        return $item->fresh();
    }

    public function addFoundItemImage(
        User $actor,
        int|FoundItem $item,
        FoundItemImageVisibility $visibility,
        UploadedFile|string $file
    ): FoundItemImage {
        LostAndFoundAuthorization::authorizeUser($actor, 'lost_found.items.edit');

        if (is_int($item)) {
            $item = $this->itemRepository->findOrFail($item);
        }

        return $this->imageService->addImage($item, $actor, $visibility, $file);
    }
}
