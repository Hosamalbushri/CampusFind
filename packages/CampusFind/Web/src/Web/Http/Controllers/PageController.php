<?php

namespace CampusFind\Web\Web\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PageController extends Controller
{
    public function show(Request $request, string $page = 'about'): View
    {
        $this->handleLocale($request);

        $allowedPages = ['about', 'how-it-works', 'faq', 'contact', 'features'];

        if (! in_array($page, $allowedPages, true)) {
            abort(404, 'Page not found');
        }

        return view('campusfind_web_web::pages.show', [
            'page' => $page,
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
