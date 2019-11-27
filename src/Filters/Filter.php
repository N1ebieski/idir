<?php

namespace N1ebieski\IDir\Filters;

use N1ebieski\ICore\Filters\Filter as BaseFilter;

/**
 * [abstract description]
 */
abstract class Filter extends BaseFilter
{
    /**
     * [filterVisible description]
     * @param int|null $value [description]
     */
    public function filterVisible(int $value = null) : void
    {
        $this->parameters['visible'] = $value;
    }

    /**
     * [filterType description]
     * @param string|null $value [description]
     */
    public function filterType(string $value = null) : void
    {
        $this->parameters['type'] = $value;
    }
}
