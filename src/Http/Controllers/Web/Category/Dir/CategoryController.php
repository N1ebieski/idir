<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Category\Dir;

use Illuminate\Support\Facades\Response;
use N1ebieski\IDir\Models\Region\Region;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\IDir\Filters\Web\Category\ShowFilter;
use N1ebieski\IDir\Http\Requests\Web\Category\ShowRequest;
use N1ebieski\IDir\Http\Controllers\Web\Category\Dir\Polymorphic;

class CategoryController implements Polymorphic
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
    public function show(Category $category, Region $region, ShowRequest $request, ShowFilter $filter) : HttpResponse
    {
        return Response::view('idir::web.category.dir.show', [
            'dirs' => $category->makeCache()->rememberDirsByFilter(
                $filter->all() + ['region' => $region->slug],
                $request->input('page') ?? 1
            ),
            'region' => $region,
            'filter' => $filter->all(),
            'category' => $category->makeCache()->rememberLoadNestedWithMorphsCountByFilter(
                ['region' => $region->slug]
            ),
            'catsAsArray' => [
                'ancestors' => $category->ancestors->pluck('id')->toArray(),
                'self' => [$category->id]
            ]
        ]);
    }
}
