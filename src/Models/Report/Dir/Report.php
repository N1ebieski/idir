<?php

namespace N1ebieski\IDir\Models\Report\Dir;

use N1ebieski\ICore\Models\Report\Report as BaseReportModel;
use N1ebieski\IDir\Models\Dir;

class Report extends BaseReportModel
{
    /**
     * [protected description]
     * @var Dir
     */
    protected $morph;

    // Accessors

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute() : string
    {
        return 'dir';
    }

    // Setters

    /**
     * [setMorph description]
     * @param Dir $dir [description]
     * @return $this
     */
    public function setMorph(Dir $dir)
    {
        $this->morph = $dir;

        return $this;
    }

    // Makers

    /**
     * [getMorph description]
     * @return Dir [description]
     */
    public function getMorph()
    {
        return $this->morph;
    }
}
