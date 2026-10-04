<?php

namespace CampusFind\LostAndFound\Services\Application;

use CampusFind\LostAndFound\Enums\FoundItemImageVisibility;
use CampusFind\LostAndFound\Enums\FoundItemSubmissionChannel;
use CampusFind\LostAndFound\Enums\ItemStatus;
use CampusFind\LostAndFound\Models\FoundItem;
use CampusFind\LostAndFound\Models\FoundItemImage;
use CampusFind\LostAndFound\Services\FoundItemImageService;
use CampusFind\Student\Models\Student;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Throwable;

class PublicFoundItemIntakeApplicationService
{
    public function __construct(private FoundItemImageService $imageService) {}

    /**
     * @param  array{category_id: int, title: string, found_location: string, found_at?: string|null, description?: string|null, dropoff_location?: string|null}  $data
     */
    public function submit(?Student $actor, array $data, ?UploadedFile $image = null): FoundItem
    {
        if ($actor !== null && ! $actor->exists) {
            throw new InvalidArgumentException('Authenticated student intake requires a persisted student.');
        }

        $title = trim((string) ($data['title'] ?? ''));
        $foundLocation = trim((string) ($data['found_location'] ?? ''));

        if ($title === '' || $foundLocation === '') {
            throw new InvalidArgumentException('Found-item title and location are required.');
        }

        $createdImage = null;

        try {
            return DB::transaction(function () use ($actor, $data, $title, $foundLocation, $image, &$createdImage): FoundItem {
                $studentId = $actor?->getKey();
                $item = FoundItem::query()->create([
                    'public_reference' => 'LF-'.now()->format('Y').'-'.strtoupper(Str::random(8)),
                    'category_id' => (int) $data['category_id'],
                    'logged_by_user_id' => null,
                    'submission_channel' => $actor
                        ? FoundItemSubmissionChannel::STUDENT_SELF_SERVICE
                        : FoundItemSubmissionChannel::PUBLIC_ANONYMOUS,
                    'reporter_student_id' => $studentId,
                    'submitted_by_student_id' => $studentId,
                    'intake_employee_user_id' => null,
                    'status' => ItemStatus::DRAFT,
                    'title' => $title,
                    'public_description' => $data['description'] ?? null,
                    'found_location' => $foundLocation,
                    'found_at' => ! empty($data['found_at']) ? Carbon::parse($data['found_at']) : now(),
                    'reported_at' => now(),
                ]);

                $dropoffLocation = trim((string) ($data['dropoff_location'] ?? ''));
                if ($dropoffLocation !== '') {
                    $item->privateDetail()->create([
                        'staff_notes' => 'Finder-selected drop-off point: '.$dropoffLocation,
                    ]);
                }

                if ($image !== null) {
                    $createdImage = $this->imageService->addImage(
                        $item,
                        null,
                        FoundItemImageVisibility::PUBLIC_SAFE,
                        $image,
                    );
                }

                return $item->refresh();
            });
        } catch (Throwable $exception) {
            $this->compensateImage($createdImage);

            throw $exception;
        }
    }

    private function compensateImage(?FoundItemImage $image): void
    {
        if ($image === null || ! isset($image->storage_key)) {
            return;
        }

        Storage::disk('public')->delete($image->storage_key);
    }
}
