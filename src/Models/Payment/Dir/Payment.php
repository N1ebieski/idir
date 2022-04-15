<?php

namespace N1ebieski\IDir\Models\Payment\Dir;

use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use N1ebieski\IDir\Models\Payment\Payment as BasePayment;
use N1ebieski\IDir\Database\Factories\Payment\Dir\PaymentFactory;

class Payment extends BasePayment
{
    // Configurations

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return \N1ebieski\IDir\Models\Payment\Payment::class;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return PaymentFactory
     */
    protected static function newFactory()
    {
        return \N1ebieski\IDir\Database\Factories\Payment\Dir\PaymentFactory::new();
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function group(): HasOneThrough
    {
        return $this->hasOneThrough(
            \N1ebieski\IDir\Models\Group::class,
            \N1ebieski\IDir\Models\Price::class,
            'id',
            'id',
            'order_id',
            'group_id'
        );
    }

    // Accessors

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getModelTypeAttribute(): string
    {
        return \N1ebieski\IDir\Models\Dir::class;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getOrderModelTypeAttribute(): string
    {
        return \N1ebieski\IDir\Models\Price::class;
    }

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute(): string
    {
        return 'dir';
    }

    // Factories

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return PaymentFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
