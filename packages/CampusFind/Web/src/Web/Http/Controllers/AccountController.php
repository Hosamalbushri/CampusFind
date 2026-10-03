<?php

namespace CampusFind\Web\Web\Http\Controllers;

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

        return view('campusfind_web_web::account.dashboard', [
            'user' => $user,
        ]);
    }
}
