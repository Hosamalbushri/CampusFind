<?php

namespace CampusFind\Web\Web\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use CampusFind\LostAndFound\Contracts\PublicLostAndFoundReadContract;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $this->handleLocale($request);

        $recentItems = [];
        $categories = [];

        if (app()->bound(PublicLostAndFoundReadContract::class)) {
            /** @var PublicLostAndFoundReadContract $reader */
            $reader = app()->make(PublicLostAndFoundReadContract::class);
            $recentItems = $reader->getRecentPublicFoundItems(6);
            $categories = $reader->getPublicCategories();
        }

        return view('campusfind_web_web::home.index', [
            'recentItems' => $recentItems,
            'categories'  => $categories,
        ]);
    }

    /**
     * Serve dynamic CSP-compliant branding stylesheet from 'self'.
     */
    public function brandingCss(): Response
    {
        $rawColor = (string) config('campusfind_web_web.branding.color', '#0E90D9');
        $color = preg_match('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', $rawColor) === 1
            ? $rawColor
            : '#0E90D9';

        $css = ":root {\n    --brand-color: {$color};\n}\n";

        return response($css, 200, [
            'Content-Type'  => 'text/css; charset=UTF-8',
            'Cache-Control' => 'public, max-age=86400',
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
