<?php

namespace N1ebieski\IDir\Filters\Admin\Dir;

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
    protected $filters = ['search', 'status', 'group', 'category', 'report', 'author', 'orderby', 'paginate'];
}
