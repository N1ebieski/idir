<?php

namespace N1ebieski\IDir\Models\Comment\Dir;

use N1ebieski\ICore\Models\Comment\Comment as CommentBaseModel;

/**
 * [Comment description]
 */
class Comment extends CommentBaseModel
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
        return 'N1ebieski\\ICore\\Models\\Comment\\Comment';
    }
}
