<?php

namespace N1ebieski\IDir\Models\Group\Dir;

use N1ebieski\IDir\Models\Group\Group as GroupBaseModel;

/**
 * [Group description]
 */
class Group extends GroupBaseModel
{
    // Configuration

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'model_type' => 'N1ebieski\\IDir\\Models\\Dir'
    ];

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return 'N1ebieski\\IDir\\Models\\Group\\Group';
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
