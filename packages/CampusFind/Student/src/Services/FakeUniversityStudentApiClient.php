<?php

namespace CampusFind\Student\Services;

use CampusFind\Student\Contracts\UniversityStudentApiContract;
use CampusFind\Student\DataTransferObjects\StudentProfileDto;
use CampusFind\Student\Services\Exceptions\UniversityApiException;

class FakeUniversityStudentApiClient implements UniversityStudentApiContract
{
    public function verifyAndFetchProfile(string $universityCardNumber, string $password): StudentProfileDto
    {
        if (strlen($password) < 4) {
            throw new UniversityApiException(__('student::app.university.invalid_credentials'));
        }

        return new StudentProfileDto(
            name: 'Demo Student ('.$universityCardNumber.')',
            registrationNumber: $universityCardNumber,
            major: 'Demo Major',
            academicLevel: '1',
        );
    }
}
