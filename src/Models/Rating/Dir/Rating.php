<?php

namespace N1ebieski\IDir\Models\Rating\Dir;

use N1ebieski\ICore\Models\Rating\Rating as RatingBaseModel;
use N1ebieski\IDir\Models\Dir;

class Rating extends RatingBaseModel
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

    // Getters

    /**
     * [getMorph description]
     * @return Dir [description]
     */
    public function getMorph()
    {
        return $this->morph;
    }
}
