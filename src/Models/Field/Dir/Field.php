<?php

namespace N1ebieski\IDir\Models\Field\Dir;

use N1ebieski\IDir\Models\Field\Field as BaseFieldModel;

class Field extends BaseFieldModel
{
    // Configurations

    /**
     * Undocumented variable
     *
     * @var string
     */
    public $path = 'vendor/idir/dirs/fields';

    // Accessors

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute(): string
    {
        return 'dir';
    }

    /**
     * [getModelTypeAttribute description]
     * @return string [description]
     */
    public function getModelTypeAttribute()
    {
        return \N1ebieski\IDir\Models\Dir::class;
    }

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return \N1ebieski\IDir\Models\Field\Field::class;
    }
}
