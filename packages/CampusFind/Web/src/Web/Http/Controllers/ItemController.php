<?php

namespace CampusFind\Web\Web\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use CampusFind\LostAndFound\Contracts\PublicLostAndFoundReadContract;
use CampusFind\LostAndFound\DataTransferObjects\PublicFoundItemSearchCriteria;
use CampusFind\LostAndFound\DataTransferObjects\PublicFoundItemSearchResult;

class ItemController extends Controller
{
    public function index(Request $request): View
    {
        $this->handleLocale($request);

        $query = $request->query('query');
        $category = $request->query('category');
        $page = (int) $request->query('page', 1);

        $reader = app()->bound(PublicLostAndFoundReadContract::class)
            ? app()->make(PublicLostAndFoundReadContract::class)
            : null;

        if ($reader !== null) {
            $criteria = new PublicFoundItemSearchCriteria(
                query: is_string($query) ? $query : null,
                category: is_string($category) ? $category : null,
                page: max(1, $page),
                perPage: 12,
            );

            $searchResult = $reader->searchPublicFoundItems($criteria);
            $categories = $reader->getPublicCategories();
        } else {
            $searchResult = new PublicFoundItemSearchResult(
                items: [],
                total: 0,
                perPage: 12,
                currentPage: 1,
                lastPage: 1,
            );
            $categories = [];
        }

        return view('campusfind_web_web::items.index', [
            'searchResult' => $searchResult,
            'categories'   => $categories,
            'query'        => $query,
            'category'     => $category,
        ]);
    }

    public function show(Request $request, string $reference): View
    {
        $this->handleLocale($request);

        if (! app()->bound(PublicLostAndFoundReadContract::class)) {
            throw new NotFoundHttpException('Lost and Found service is unavailable.');
        }

        /** @var PublicLostAndFoundReadContract $reader */
        $reader = app()->make(PublicLostAndFoundReadContract::class);
        $item = $reader->findPublicFoundItemByReference($reference);

        if ($item === null) {
            abort(404, 'Item not found');
        }

        return view('campusfind_web_web::items.show', [
            'item' => $item,
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
