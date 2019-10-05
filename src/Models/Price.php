<?php

namespace N1ebieski\IDir\Models;

use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Services\PriceService;

/**
 * [Price description]
 */
class Price extends Model
{
    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'price',
        'days'
    ];

    // Relations

    /**
     * [group description]
     * @return [type] [description]
     */
    public function group()
    {
        return $this->belongsTo('N1ebieski\IDir\Models\Group');
    }

    /**
     * [getService description]
     * @return PriceService [description]
     */
    public function getService() : PriceService
    {
        return app()->make(PriceService::class, ['price' => $this]);
    }
}
