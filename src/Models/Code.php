<?php

namespace N1ebieski\IDir\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use N1ebieski\IDir\Models\Price;
use N1ebieski\IDir\Services\CodeService;
use N1ebieski\IDir\Repositories\CodeRepo;

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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'price_id' => 'integer',
        'quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
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
    public function makeService()
    {
        return App::make(CodeService::class, ['code' => $this]);
    }

    /**
     * [makeRepo description]
     * @return CodeRepo [description]
     */
    public function makeRepo()
    {
        return App::make(CodeRepo::class, ['code' => $this]);
    }
}
