<?php

namespace N1ebieski\IDir\Filters\Admin\Group;

use N1ebieski\ICore\Filters\Filter;

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

    public function filterVisible(int $value = null) : void
    {
        $this->parameters['visible'] = $value;
    }
}
