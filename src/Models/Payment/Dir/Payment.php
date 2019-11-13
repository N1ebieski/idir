<?php

namespace N1ebieski\IDir\Models\Payment\Dir;

use N1ebieski\IDir\Models\Payment\Payment as PaymentBaseModel;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Price;

/**
 * [Payment description]
 */
class Payment extends PaymentBaseModel
{
    /**
     * [protected description]
     * @var Dir
     */
    protected $morph;

    /**
     * [protected description]
     * @var Price
     */
    protected $price_morph;

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

    /**
     * [setPriceMorph description]
     * @param Price $price [description]
     * @return $this
     */
    public function setPriceMorph(Price $price)
    {
        $this->price_morph = $price;

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

    /**
     * [getPrice description]
     * @return Price [description]
     */
    public function getPriceMorph()
    {
        return $this->price_morph;
    }
}
