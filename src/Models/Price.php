<?php

namespace N1ebieski\IDir\Models;

use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Services\PriceService;
use Illuminate\Database\Eloquent\Collection;

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
        'days',
        'code',
        'number'
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
     * [codes description]
     * @return [type] [description]
     */
    public function codes()
    {
        return $this->hasMany('N1ebieski\IDir\Models\Code');
    }

    // Accessors

    /**
     * [getCodesAsStringAttribute description]
     * @return string|null [description]
     */
    public function getCodesAsStringAttribute() : ?string
    {
        if ($this->codes instanceof Collection && $this->codes->isNotEmpty()) {
            foreach ($this->codes as $code) {
                $codes[] = $code->code . ($code->quantity !== null ? '|' . $code->quantity : null);
            }

            return (string)implode("\r\n", $codes);
        }

        return $this->codes['codes'] ?? null;
    }

    // Getters

    /**
     * [getService description]
     * @return PriceService [description]
     */
    public function getService() : PriceService
    {
        return app()->make(PriceService::class, ['price' => $this]);
    }
}
