<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Category\Dir;

use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\ICore\Http\Requests\Web\Category\SearchRequest;
use N1ebieski\ICore\Http\Responses\Web\Category\SearchResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Filters\Web\Category\ShowFilter;
use N1ebieski\IDir\Http\Requests\Web\Category\ShowRequest;
use N1ebieski\IDir\Models\Region\Region;

/**
 * [interface description]
 */
interface Polymorphic
{
    /**
     * Display a listing of the Dirs for Category.
     *
     * @param  Category $category [description]
     * @param  Region   $region   [description]
     * @param  ShowRequest $request
     * @param  ShowFilter $filter
     * @return HttpResponse       [description]
     */
    public function show(Category $category, Region $region, ShowRequest $request, ShowFilter $filter) : HttpResponse;

    /**
     * Search Categories for specified name.
     *
     * @param  Category      $category [description]
     * @param  SearchRequest $request  [description]
     * @param  SearchResponse $response  [description]
     * @return JsonResponse
     */
    public function search(Category $category, SearchRequest $request, SearchResponse $response) : JsonResponse;
}
