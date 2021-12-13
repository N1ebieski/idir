<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Category\Dir;

use N1ebieski\IDir\Models\Region\Region;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Loads\Web\Category\ShowLoad;
use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\IDir\Filters\Web\Category\ShowFilter;
use N1ebieski\IDir\Http\Requests\Web\Category\ShowRequest;

interface Polymorphic
{
    /**
     * Undocumented function
     *
     * @param Category $category
     * @param Region $region
     * @param ShowLoad $load
     * @param ShowRequest $request
     * @param ShowFilter $filter
     * @return HttpResponse
     */
    public function show(Category $category, Region $region, ShowLoad $load, ShowRequest $request, ShowFilter $filter): HttpResponse;
}
