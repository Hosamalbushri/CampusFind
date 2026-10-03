<?php

namespace CampusFind\Student\Http\Controllers;

use CampusFind\Student\Contracts\UniversityStudentApiContract;
use CampusFind\Student\Http\Requests\StudentLoginRequest;
use CampusFind\Student\Models\Student;
use CampusFind\Student\Services\Exceptions\UniversityApiException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class StudentSessionController extends Controller
{
    /**
     * Show the student login / first-time registration form (redirects to central Web portal login).
     */
    public function create(): RedirectResponse
    {
        if (Auth::guard('student')->check()) {
            return redirect()->to($this->determineRedirectUrl());
        }

        $this->captureIntendedRedirectFromQuery();

        if (Route::has('campusfind_web.web.login')) {
            return redirect()->route('campusfind_web.web.login');
        }

        return redirect()->to('/login');
    }

    /**
     * Determine safe redirect URL after login.
     */
    protected function determineRedirectUrl(): string
    {
        $intended = session()->pull('url.intended');
        $adminPath = trim((string) config('app.admin_path', 'admin'), '/');

        if (is_string($intended) && $intended !== '') {
            $parsedPath = parse_url($intended, PHP_URL_PATH);
            $normalizedPath = trim((string) $parsedPath, '/');

            if (
                $normalizedPath !== $adminPath
                && ! str_starts_with($normalizedPath, $adminPath . '/')
                && $normalizedPath !== 'admin'
                && ! str_starts_with($normalizedPath, 'admin/')
            ) {
                return $intended;
            }
        }

        if (Route::has('campusfind_web.web.account.dashboard')) {
            return route('campusfind_web.web.account.dashboard');
        }

        return config('student.redirect_after_login', '/');
    }

    /**
     * Allow ?intended= URL so subscribe → login → return to target flow works.
     */
    protected function captureIntendedRedirectFromQuery(): void
    {
        $raw = request()->query('intended');
        if (! is_string($raw) || $raw === '') {
            return;
        }

        $adminPath = trim((string) config('app.admin_path', 'admin'), '/');

        if (filter_var($raw, FILTER_VALIDATE_URL)) {
            $root = rtrim((string) config('app.url'), '/');
            if (str_starts_with($raw, $root)) {
                $path = trim((string) parse_url($raw, PHP_URL_PATH), '/');
                if (
                    $path !== $adminPath
                    && ! str_starts_with($path, $adminPath . '/')
                    && $path !== 'admin'
                    && ! str_starts_with($path, 'admin/')
                ) {
                    session(['url.intended' => $raw]);
                }
            }

            return;
        }

        if (str_starts_with($raw, '/') && ! str_starts_with($raw, '//')) {
            $path = trim($raw, '/');
            if (
                $path !== $adminPath
                && ! str_starts_with($path, $adminPath . '/')
                && $path !== 'admin'
                && ! str_starts_with($path, 'admin/')
            ) {
                session(['url.intended' => url($raw)]);
            }
        }
    }

    /**
     * Verify with university API (new students) or local password (returning), then start session.
     */
    public function store(StudentLoginRequest $request, UniversityStudentApiContract $universityApi): RedirectResponse
    {
        $card = $request->validated('university_card_number');
        $password = $request->validated('password');
        $remember = (bool) $request->boolean('remember');

        $existing = Student::query()->where('university_card_number', $card)->first();

        if ($existing) {
            if (! Auth::guard('student')->attempt([
                'university_card_number' => $card,
                'password' => $password,
            ], $remember)) {
                return back()
                    ->withInput($request->only('university_card_number'))
                    ->withErrors(['university_card_number' => __('student::app.login.failed')])
                    ->with('error', __('student::app.login.failed'));
            }

            $request->session()->regenerate();

            return redirect()->to($this->determineRedirectUrl())
                ->with('success', __('student::app.login.welcome_back'));
        }

        try {
            $profile = $universityApi->verifyAndFetchProfile($card, $password);
        } catch (UniversityApiException $e) {
            return back()
                ->withInput($request->only('university_card_number'))
                ->withErrors(['university_card_number' => $e->getMessage()])
                ->with('error', $e->getMessage());
        }

        $student = Student::query()->create([
            'university_card_number' => $card,
            'password' => $password,
            'name' => $profile->name,
            'registration_number' => $profile->registrationNumber,
            'major' => $profile->major,
            'academic_level' => $profile->academicLevel,
        ]);

        Auth::guard('student')->login($student, $remember);
        $request->session()->regenerate();

        return redirect()->to($this->determineRedirectUrl())
            ->with('success', __('student::app.login.registered'));
    }

    /**
     * Log the student out.
     */
    public function destroy(): RedirectResponse
    {
        Auth::guard('student')->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        if (Route::has('campusfind_web.web.login')) {
            return redirect()->route('campusfind_web.web.login')
                ->with('success', __('student::app.login.logged_out'));
        }

        return redirect()->to('/login')
            ->with('success', __('student::app.login.logged_out'));
    }
}
