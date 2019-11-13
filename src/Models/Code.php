<?php

namespace N1ebieski\IDir\Models;

use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Models\Price;
use N1ebieski\IDir\Services\CodeService;

/**
 * [Code description]
 */
class Code extends Model
{
    // Configuration

    /**
     * [protected description]
     * @var Price
     */
    protected $price;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'quantity'
    ];

    // Setters

    /**
     * @param Price $price
     *
     * @return static
     */
    public function setPrice(Price $price)
    {
        $this->price = $price;

        return $this;
    }

    // Getters

    /**
     * [getPrice description]
     * @return Price [description]
     */
    public function getPrice() : Price
    {
        return $this->price;
    }

    // Relations

    /**
     * [price description]
     * @return [type] [description]
     */
    public function price()
    {
        return $this->belongsTo('N1ebieski\IDir\Models\Price');
    }

    // Makers

    /**
     * [makeService description]
     * @return CodeService [description]
     */
    public function makeService() : CodeService
    {
        return app()->make(CodeService::class, ['code' => $this]);
    }
}
