<?php

namespace N1ebieski\IDir\Models\Field\Dir;

use N1ebieski\IDir\Models\Field\Field as BaseFieldModel;

/**
 * [Field description]
 */
class Field extends BaseFieldModel
{
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
}
