<?php

namespace CampusFind\Web\Web\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use CampusFind\Student\Contracts\UniversityStudentApiContract;
use CampusFind\Student\Models\Student;
use CampusFind\Student\Services\Exceptions\UniversityApiException;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $this->handleLocale($request);

        $guard = config('campusfind_web_web.auth.guard', 'student');

        if (Auth::guard($guard)->check()) {
            return redirect()->route('campusfind_web.web.account.dashboard');
        }

        $this->captureIntendedRedirectFromQuery($request);

        return view('campusfind_web_web::auth.login');
    }

    /**
     * Handle login authentication attempt.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $this->handleLocale($request);

        $request->validate([
            'university_card_number' => 'required|string|max:64',
            'password'               => 'required|string',
            'remember'               => 'nullable|boolean',
        ]);

        $card = (string) $request->input('university_card_number');
        $password = (string) $request->input('password');
        $remember = (bool) $request->boolean('remember');

        $guard = config('campusfind_web_web.auth.guard', 'student');

        // Check if student exists locally in DB
        $existing = Student::query()->where('university_card_number', $card)->first();

        if ($existing) {
            if (! Auth::guard($guard)->attempt([
                'university_card_number' => $card,
                'password'               => $password,
            ], $remember)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => trans('campusfind_web_web::app.web.auth.login_failed'),
                        'errors'  => [
                            'university_card_number' => [trans('campusfind_web_web::app.web.auth.login_failed')],
                        ],
                    ], 422);
                }

                return back()
                    ->withInput($request->only('university_card_number'))
                    ->withErrors(['university_card_number' => trans('campusfind_web_web::app.web.auth.login_failed')]);
            }

            $request->session()->regenerate();

            if ($request->expectsJson()) {
                return response()->json([
                    'message'      => trans('campusfind_web_web::app.web.auth.welcome_back'),
                    'redirect_url' => $this->determineRedirectUrl(),
                ], 200);
            }

            return redirect()->to($this->determineRedirectUrl())
                ->with('success', trans('campusfind_web_web::app.web.auth.welcome_back'));
        }

        // If not in local database, verify via University API if available
        if (app()->bound(UniversityStudentApiContract::class)) {
            try {
                /** @var UniversityStudentApiContract $universityApi */
                $universityApi = app()->make(UniversityStudentApiContract::class);
                $profile = $universityApi->verifyAndFetchProfile($card, $password);

                $student = Student::query()->create([
                    'university_card_number' => $card,
                    'password'               => $password,
                    'name'                   => $profile->name,
                    'registration_number'    => $profile->registrationNumber,
                    'major'                  => $profile->major,
                    'academic_level'         => $profile->academicLevel,
                ]);

                Auth::guard($guard)->login($student, $remember);
                $request->session()->regenerate();

                if ($request->expectsJson()) {
                    return response()->json([
                        'message'      => trans('campusfind_web_web::app.web.auth.welcome_back'),
                        'redirect_url' => $this->determineRedirectUrl(),
                    ], 200);
                }

                return redirect()->to($this->determineRedirectUrl())
                    ->with('success', trans('campusfind_web_web::app.web.auth.welcome_back'));
            } catch (UniversityApiException $e) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => $e->getMessage(),
                        'errors'  => [
                            'university_card_number' => [$e->getMessage()],
                        ],
                    ], 422);
                }

                return back()
                    ->withInput($request->only('university_card_number'))
                    ->withErrors(['university_card_number' => $e->getMessage()]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => trans('campusfind_web_web::app.web.auth.login_failed'),
                'errors'  => [
                    'university_card_number' => [trans('campusfind_web_web::app.web.auth.login_failed')],
                ],
            ], 422);
        }

        return back()
            ->withInput($request->only('university_card_number'))
            ->withErrors(['university_card_number' => trans('campusfind_web_web::app.web.auth.login_failed')]);
    }

    /**
     * Log out of the session.
     */
    public function destroy(Request $request): JsonResponse|RedirectResponse
    {
        $guard = config('campusfind_web_web.auth.guard', 'student');

        Auth::guard($guard)->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'message'      => trans('campusfind_web_web::app.web.auth.logout_success'),
                'redirect_url' => route('campusfind_web.web.home'),
            ], 200);
        }

        return redirect()->route('campusfind_web.web.home')
            ->with('success', trans('campusfind_web_web::app.web.auth.logout_success'));
    }

    /**
     * Determine safe redirect URL after successful student login.
     * Prevents unintended redirects to admin routes or unauthorized areas.
     */
    protected function determineRedirectUrl(): string
    {
        $intended = session()->pull('url.intended');
        $adminPath = trim((string) config('app.admin_path', 'admin'), '/');

        if (is_string($intended) && $intended !== '') {
            $parsedPath = parse_url($intended, PHP_URL_PATH);
            $normalizedPath = trim((string) $parsedPath, '/');

            // Block any redirection to admin panel routes or admin login
            if (
                $normalizedPath !== $adminPath
                && ! str_starts_with($normalizedPath, $adminPath . '/')
                && $normalizedPath !== 'admin'
                && ! str_starts_with($normalizedPath, 'admin/')
            ) {
                return $intended;
            }
        }

        return route('campusfind_web.web.account.dashboard');
    }

    private function captureIntendedRedirectFromQuery(Request $request): void
    {
        $raw = $request->query('intended');
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

    private function handleLocale(Request $request): void
    {
        $availableLocales = ['ar', 'en', 'es', 'fa', 'pt_BR', 'tr', 'vi'];

        if ($request->has('locale') && in_array($request->query('locale'), $availableLocales, true)) {
            app()->setLocale($request->query('locale'));
            session(['locale' => $request->query('locale')]);
        } elseif (session()->has('locale') && in_array(session('locale'), $availableLocales, true)) {
            app()->setLocale(session('locale'));
        }
    }
}
