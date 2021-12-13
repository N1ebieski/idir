<?php

namespace N1ebieski\IDir\Loads\Web\Category\Dir;

use Illuminate\Http\Request;
use N1ebieski\IDir\Models\Region\Region;
use N1ebieski\IDir\Filters\Web\Category\Dir\ShowFilter;

class ShowLoad
{
    /**
     * Undocumented variable
     *
     * @var ShowFilter
     */
    protected $filter;

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param ShowFilter $filter
     */
    public function __construct(Request $request, ShowFilter $filter)
    {
        if ($request->route('region_cache') instanceof Region) {
            $filter->setRegion($request->route('region_cache'));
        }
    }
}
