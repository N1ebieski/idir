<?php

namespace N1ebieski\IDir\Filters\Web\Category\Dir;

use Illuminate\Http\Request;
use N1ebieski\ICore\Filters\Filter;
use N1ebieski\IDir\Models\Region\Region;
use N1ebieski\IDir\Filters\Traits\HasRegion;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Filters\Traits\HasOrderBy;

class ShowFilter extends Filter
{
    use HasOrderBy;
    use HasRegion;

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param Collect $collect
     */
    public function __construct(Request $request, Collect $collect)
    {
        parent::__construct($request, $collect);

        if ($request->route('region_cache') instanceof Region) {
            $this->setRegion($request->route('region_cache'));
        }
    }
}
