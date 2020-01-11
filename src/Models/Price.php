<?php

namespace N1ebieski\IDir\Models;

use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Services\PriceService;
use N1ebieski\IDir\Repositories\PriceRepo;
use Illuminate\Database\Eloquent\Collection;

/**
 * [Price description]
 */
class Price extends Model
{
    // Configuration

    /**
     * [protected description]
     * @var Group
     */
    protected $group;

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

    // Setters

    /**
     * @param Group $group
     *
     * @return static
     */
    public function setGroup(Group $group)
    {
        $this->group = $group;

        return $this;
    }

    // Getters

    /**
     * [getGroup description]
     * @return Group [description]
     */
    public function getGroup() : Group
    {
        return $this->group;
    }

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

    // Makers

    /**
     * [makeService description]
     * @return PriceService [description]
     */
    public function makeService()
    {
        return app()->make(PriceService::class, ['price' => $this]);
    }

    /**
     * [makeRepo description]
     * @return PriceRepo [description]
     */
    public function makeRepo()
    {
        return app()->make(PriceRepo::class, ['price' => $this]);
    }
}
