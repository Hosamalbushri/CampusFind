<?php

namespace CampusFind\Web\Web\Http\Controllers;

use CampusFind\LostAndFound\Models\FoundItem;
use CampusFind\LostAndFound\Models\FoundReportResponse;
use CampusFind\LostAndFound\Models\LostFoundClaim;
use CampusFind\LostAndFound\Models\LostReport;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AccountController extends Controller
{
    /**
     * Display the authenticated user dashboard.
     *
     * @throws \RuntimeException
     */
    public function dashboard(Request $request): View
    {
        $this->handleLocale($request);

        $authConfig = config('campusfind_web_web.auth', []);
        $enabled = (bool) ($authConfig['enabled'] ?? false);

        if (! $enabled) {
            throw new \RuntimeException(
                'Authentication is disabled for package [campusfind_web].'
            );
        }

        $guard = $authConfig['guard'] ?? null;
        if (! $guard || ! config("auth.guards.{$guard}")) {
            throw new \RuntimeException(sprintf(
                'AccountController error: Package [%s] specifies guard [%s], which is not defined in config/auth.php.',
                'campusfind_web',
                $guard ?? 'null'
            ));
        }

        $user = auth()->guard($guard)->user();

        $reports = [];
        $foundReports = [];
        $claims = [];
        $foundResponses = [];
        if ($user) {
            if (class_exists(LostReport::class)) {
                $reports = LostReport::where('student_id', $user->id)
                    ->with('category')
                    ->orderByDesc('id')
                    ->get();
            }

            if (class_exists(FoundItem::class)) {
                $foundReports = FoundItem::where('submitted_by_student_id', $user->id)
                    ->with('category')
                    ->orderByDesc('id')
                    ->get();
            }

            if (class_exists(LostFoundClaim::class)) {
                $claims = LostFoundClaim::where('claimant_student_id', $user->id)
                    ->with(['foundItem.category'])
                    ->orderByDesc('id')
                    ->get();
            }

            if (class_exists(FoundReportResponse::class)) {
                $foundResponses = FoundReportResponse::where('responder_student_id', $user->id)
                    ->with('lostReport:id,public_reference,title,status')
                    ->orderByDesc('id')
                    ->get();
            }
        }

        return view('campusfind_web_web::account.dashboard', [
            'user' => $user,
            'reports' => $reports,
            'foundReports' => $foundReports,
            'claims' => $claims,
            'foundResponses' => $foundResponses,
        ]);
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
