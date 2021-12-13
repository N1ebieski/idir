<?php

namespace N1ebieski\IDir\Filters\Traits;

use N1ebieski\IDir\Models\Region\Region;

trait HasRegion
{
    /**
     * [setRegion description]
     * @param Region $region [description]
     */
    public function setRegion(Region $region)
    {
        $this->parameters['region'] = $region;

        return $this;
    }

    /**
     * [filterRegion description]
     * @param  int|null $id [description]
     * @return Region|0     [description]
     */
    public function filterRegion(int $id = null)
    {
        $this->parameters['region'] = null;

        if ($id !== null) {
            if ($region = $this->findRegion($id)) {
                return $this->setRegion($region);
            }
        }
    }

    /**
     * [findRegion description]
     * @param  int|null   $id [description]
     * @return Region     [description]
     */
    public function findRegion(int $id = null): Region
    {
        return Region::find($id);
    }
}
