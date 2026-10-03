<?php

use CampusFind\Student\Contracts\UniversityStudentApiContract;
use CampusFind\Student\DataTransferObjects\StudentProfileDto;
use CampusFind\Student\Models\Student;
use CampusFind\Student\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

uses(TestCase::class, DatabaseTransactions::class);

it('redirects a student guest to the student login', function () {
    Route::middleware('auth:student')->get('student/test-safety-guest', fn () => 'ok');

    $this->get('/student/test-safety-guest')
        ->assertRedirect();
});

it('returns JSON 401 for an unauthenticated student API-style request', function () {
    Route::middleware('auth:student')->get('student/test-safety-api', fn () => 'ok');

    $this->getJson('/student/test-safety-api')
        ->assertUnauthorized();
});

it('uses the university API only for first student login then retains local authentication', function () {
    $this->get(route('student.login'))->assertRedirect();

    $card = 'AUDIT-'.uniqid();
    $api = Mockery::mock(UniversityStudentApiContract::class);
    $api->shouldReceive('verifyAndFetchProfile')
        ->once()
        ->with($card, 'first-password')
        ->andReturn(new StudentProfileDto('Audit Student', $card, 'Audit Major', '4'));
    app()->instance(UniversityStudentApiContract::class, $api);

    $this->post(route('student.login.store'), [
        'university_card_number' => $card,
        'password' => 'first-password',
    ])->assertRedirect();

    $student = Student::where('university_card_number', $card)->firstOrFail();
    expect(Hash::check('first-password', $student->password))->toBeTrue();
    auth()->guard('student')->logout();

    $unusedApi = Mockery::mock(UniversityStudentApiContract::class);
    $unusedApi->shouldNotReceive('verifyAndFetchProfile');
    app()->instance(UniversityStudentApiContract::class, $unusedApi);

    $this->post(route('student.login.store'), [
        'university_card_number' => $card,
        'password' => 'first-password',
    ])->assertRedirect();

    $this->assertAuthenticatedAs($student, 'student');
});
