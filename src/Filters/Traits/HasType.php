<?php

namespace N1ebieski\IDir\Filters\Traits;

/**
 * [trait description]
 */
trait HasType
{
    /**
     * [filterType description]
     * @param string|null $value [description]
     */
    public function filterType(string $value = null) : void
    {
        $this->parameters['type'] = $value;
    }
}
