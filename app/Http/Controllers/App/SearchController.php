<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\SearchesLinks;
use App\Http\Requests\SearchRequest;
use App\Models\LinkList;
use App\Models\Tag;
use Illuminate\Contracts\View\View;

class SearchController extends Controller
{
    use SearchesLinks;

    public function getSearch(): View
    {
        return view('app.search.search', [
            'pageTitle' => trans('search.search'),
            'all_tags' => Tag::visibleForUser()->with('user:id,name')->get(['name', 'id', 'user_id']),
            'all_lists' => LinkList::visibleForUser()->with('user:id,name')->get(['name', 'id', 'user_id']),
        ])
            ->with('results', collect([]))
            ->with('order_by_options', $this->orderByOptions)
            ->with('query_settings', [
                'old_query' => null,
                'search_title' => true,
                'search_description' => true,
                'visibility' => null,
                'broken_only' => false,
                'empty_tags' => false,
                'empty_lists' => false,
                'only_lists' => [],
                'only_tags' => [],
                'order_by' => $this->orderByOptions[0],
                'performed_search' => false,
            ]);
    }

    public function doSearch(SearchRequest $request): View
    {
        $search = $this->buildDatabaseQuery($request);
        $results = $search->paginate(getPaginationLimit());

        return view('app.search.search', [
            'pageTitle' => trans('search.results_for') . ' ' . $this->searchQuery,
            'all_tags' => Tag::visibleForUser()->with('user:id,name')->get(['name', 'id', 'user_id']),
            'all_lists' => LinkList::visibleForUser()->with('user:id,name')->get(['name', 'id', 'user_id']),
        ])
            ->with('results', $results)
            ->with('order_by_options', $this->orderByOptions)
            ->with('query_settings', [
                'old_query' => $this->searchQuery,
                'search_title' => $this->searchTitle,
                'search_description' => $this->searchDescription,
                'visibility' => $this->searchVisibility,
                'broken_only' => $this->searchBrokenOnly,
                'only_lists' => $this->searchLists,
                'only_tags' => $this->searchTags,
                'empty_tags' => $this->emptyTags,
                'empty_lists' => $this->emptyLists,
                'order_by' => $this->searchOrderBy,
                'performed_search' => true,
            ]);
    }
}
