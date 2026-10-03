<?php

namespace CampusFind\LostAndFound\Services\Application;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use CampusFind\LostAndFound\Enums\ReportStatus;
use CampusFind\LostAndFound\Models\LostReport;
use CampusFind\LostAndFound\Models\LostReportImage;
use CampusFind\LostAndFound\Repositories\LostReportRepository;
use CampusFind\LostAndFound\Services\LostReportImageService;
use CampusFind\Student\Models\Student;

class StudentReportApplicationService
{
    public function __construct(
        protected LostReportRepository $reportRepository,
        protected LostReportImageService $imageService
    ) {}

    public function createLostReport(Student $actor, array $data): LostReport
    {
        $data['student_id'] = $actor->id;
        $data['status'] = ReportStatus::DRAFT->value;
        unset($data['resolved_found_item_id']);

        if (empty($data['public_reference'])) {
            $data['public_reference'] = 'LR-'.strtoupper(Str::random(10));
        }

        return $this->reportRepository->create($data);
    }

    public function updateOwnLostReport(Student $actor, LostReport $report, array $data): LostReport
    {
        LostAndFoundAuthorization::authorizeStudentOwnership($actor, $report->student_id, 'LostReport');

        return $this->reportRepository->update($data, $report->id);
    }

    public function addOwnLostReportImage(
        Student $actor,
        LostReport $report,
        UploadedFile $file
    ): LostReportImage {
        LostAndFoundAuthorization::authorizeStudentOwnership($actor, $report->student_id, 'LostReport');

        return $this->imageService->addImage($report, $file);
    }
}
