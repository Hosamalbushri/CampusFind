<?php

namespace CampusFind\LostAndFound\Services\Application;

use CampusFind\LostAndFound\Enums\FoundResponseStatus;
use CampusFind\LostAndFound\Enums\ReportStatus;
use CampusFind\LostAndFound\Models\FoundReportResponse;
use CampusFind\LostAndFound\Models\LostReport;
use CampusFind\LostAndFound\Services\FoundResponseImageService;
use CampusFind\LostAndFound\Services\FoundResponseStateService;
use CampusFind\LostAndFound\Services\SecurityInvariants;
use CampusFind\Student\Models\Student;
use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Throwable;

class StudentFoundResponseApplicationService
{
    public function __construct(private FoundResponseImageService $imageService) {}

    /**
     * @param  array{found_location: string, found_at?: string|null, dropoff_location: string, message?: string|null}  $data
     * @param  list<UploadedFile>  $images
     */
    public function submit(Student $actor, int $lostReportId, array $data, array $images = []): FoundReportResponse
    {
        $foundLocation = trim($data['found_location'] ?? '');
        $dropoffLocation = trim($data['dropoff_location'] ?? '');
        $message = isset($data['message']) ? trim((string) $data['message']) : null;

        if ($foundLocation === '' || $dropoffLocation === '') {
            throw new InvalidArgumentException('Found and drop-off locations are required.');
        }

        if (mb_strlen($foundLocation) > 255 || ($message !== null && mb_strlen($message) > 2000)) {
            throw new InvalidArgumentException('Found-response text exceeds its allowed length.');
        }

        if (! in_array($dropoffLocation, config('lost_found.found_responses.approved_dropoff_locations', []), true)) {
            throw new InvalidArgumentException('The selected drop-off location is not approved.');
        }

        $foundAt = null;
        if (! empty($data['found_at'])) {
            try {
                $foundAt = Carbon::parse($data['found_at']);
            } catch (Throwable) {
                throw new InvalidArgumentException('The found date is invalid.');
            }

            if ($foundAt->isFuture()) {
                throw new InvalidArgumentException('The found date cannot be in the future.');
            }
        }

        if ($message !== null && $message !== '') {
            SecurityInvariants::assertNoAuthSecrets($message);
        }

        $prepared = $this->imageService->prepare($images);

        try {
            return DB::transaction(function () use (
                $actor,
                $lostReportId,
                $foundAt,
                $foundLocation,
                $dropoffLocation,
                $message,
                $prepared,
            ): FoundReportResponse {
                $report = LostReport::query()->lockForUpdate()->findOrFail($lostReportId);

                if ($report->status !== ReportStatus::ACTIVE || $report->resolved_found_item_id !== null) {
                    throw new DomainException('Only an unresolved active lost report can receive found responses.');
                }

                if ((int) $report->student_id === (int) $actor->getKey()) {
                    throw new DomainException('Self-responses are disabled until an explicit project policy is approved.');
                }

                if (FoundReportResponse::query()
                    ->where('lost_report_id', $report->getKey())
                    ->where('responder_student_id', $actor->getKey())
                    ->lockForUpdate()
                    ->exists()) {
                    throw new DomainException('This student already submitted a response to this lost report.');
                }

                $response = FoundReportResponse::query()->create([
                    'public_reference' => 'FR-'.strtoupper(Str::random(20)),
                    'lost_report_id' => $report->getKey(),
                    'responder_student_id' => $actor->getKey(),
                    'status' => FoundResponseStatus::SUBMITTED,
                    'found_location' => $foundLocation,
                    'found_at' => $foundAt,
                    'dropoff_location' => $dropoffLocation,
                    'message' => $message !== '' ? $message : null,
                    'submitted_at' => now(),
                ]);

                $this->imageService->finalize($response, $prepared);

                return $response->refresh();
            });
        } catch (Throwable $exception) {
            $this->imageService->cleanup($prepared);

            throw $exception;
        }
    }

    public function cancelOwn(Student $actor, FoundReportResponse $response): FoundReportResponse
    {
        return DB::transaction(function () use ($actor, $response): FoundReportResponse {
            $locked = FoundReportResponse::query()->lockForUpdate()->findOrFail($response->getKey());
            LostAndFoundAuthorization::authorizeStudentOwnership($actor, $locked->responder_student_id, 'found response');

            $locked->status = FoundResponseStateService::transition(
                $locked->status,
                FoundResponseStatus::CANCELLED,
            );
            $locked->cancelled_at = now();
            $locked->save();

            return $locked->refresh();
        });
    }
}
