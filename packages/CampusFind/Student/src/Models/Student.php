<?php

namespace CampusFind\Student\Models;

use CampusFind\Student\Contracts\Student as StudentContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable implements StudentContract
{
    use Notifiable;

    /**
     * @var string
     */
    protected $table = 'students';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'university_card_number',
        'password',
        'name',
        'registration_number',
        'major',
        'academic_level',
        'profile_image',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
