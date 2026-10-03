<?php

namespace CampusFind\Student\Repositories;

use CampusFind\Student\Contracts\Student;
use Webkul\Core\Eloquent\Repository;

class StudentRepository extends Repository
{
    /**
     * Searchable fields.
     */
    protected $fieldSearchable = [
        'id',
        'name',
        'university_card_number',
        'registration_number',
        'major',
        'academic_level',
    ];

    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return Student::class;
    }
}
