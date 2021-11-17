<?php

namespace N1ebieski\IDir\Filters\Traits;

trait HasVisible
{
    /**
     * [filterVisible description]
     * @param int|null $value [description]
     */
    public function filterVisible(int $value = null): void
    {
        $this->parameters['visible'] = $value;
    }
}
