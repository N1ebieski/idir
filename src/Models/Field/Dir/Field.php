<?php

namespace N1ebieski\IDir\Models\Field\Dir;

use N1ebieski\IDir\Models\Field\Field as BaseFieldModel;
use N1ebieski\IDir\Models\Dir;

/**
 * [Field description]
 */
class Field extends BaseFieldModel
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

    /**
     * [getModelTypeAttribute description]
     * @return string [description]
     */
    public function getModelTypeAttribute()
    {
        return 'N1ebieski\\IDir\\Models\\Dir';
    }

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return 'N1ebieski\\IDir\\Models\\Field\\Field';
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
