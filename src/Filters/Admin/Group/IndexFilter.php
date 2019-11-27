<?php

namespace N1ebieski\IDir\Filters\Admin\Group;

use N1ebieski\IDir\Filters\Filter;

/**
 * [IndexFilter description]
 */
class IndexFilter extends Filter
{
    /**
     * [protected description]
     * @var array
     */
    protected $filters = ['search', 'visible', 'orderby', 'paginate'];
}
