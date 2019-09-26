<?php

namespace N1ebieski\IDir\Models\Category\Dir;

use N1ebieski\ICore\Models\Category\Category as CategoryBaseModel;

/**
 * [Category description]
 */
class Category extends CategoryBaseModel
{
    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'model_type' => 'N1ebieski\\IDir\\Models\\Dir',
        'status' => 1,
    ];

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return 'N1ebieski\\ICore\\Models\\Category\\Category';
    }

    // Accessors

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute() : string
    {
        return 'dir';
    }
}
