<?php

namespace CampusFind\Student\Contracts;

use CampusFind\Student\DataTransferObjects\StudentProfileDto;
use CampusFind\Student\Services\Exceptions\UniversityApiException;

interface UniversityStudentApiContract
{
    /**
     * Verify credentials with the university and return profile data on success.
     *
     * @throws UniversityApiException
     */
    public function verifyAndFetchProfile(string $universityCardNumber, string $password): StudentProfileDto;
}
