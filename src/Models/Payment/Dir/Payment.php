<?php

namespace N1ebieski\IDir\Models\Payment\Dir;

use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use N1ebieski\IDir\Models\Payment\Payment as PaymentBaseModel;

/**
 * [Payment description]
 */
class Payment extends PaymentBaseModel
{
    /**
     * [getModelTypeAttribute description]
     * @return [type] [description]
     */
    public function getModelTypeAttribute()
    {
        return 'N1ebieski\\IDir\\Models\\Dir';
    }

    /**
     * [getModelTypeAttribute description]
     * @return [type] [description]
     */
    public function getOrderModelTypeAttribute()
    {
        return 'N1ebieski\\IDir\\Models\\Price';
    }

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return 'N1ebieski\\IDir\\Models\\Payment\\Payment';
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function group() : HasOneThrough
    {
        return $this->hasOneThrough(
            'N1ebieski\IDir\Models\Group',
            'N1ebieski\IDir\Models\Price',
            'id',
            'id',
            'order_id',
            'group_id'
        );
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
