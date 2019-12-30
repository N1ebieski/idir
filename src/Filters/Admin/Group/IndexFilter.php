<?php

namespace N1ebieski\IDir\Filters\Admin\Group;

use N1ebieski\ICore\Filters\Filter;
use N1ebieski\ICore\Filters\Traits\HasSearch;
use N1ebieski\IDir\Filters\Traits\HasVisible;
use N1ebieski\ICore\Filters\Traits\HasOrderBy;
use N1ebieski\ICore\Filters\Traits\HasPaginate;

/**
 * [IndexFilter description]
 */
class IndexFilter extends Filter
{
    use HasSearch, HasVisible, HasOrderBy, HasPaginate;
}
