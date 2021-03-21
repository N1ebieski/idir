<?php

namespace N1ebieski\IDir\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Services\PriceService;
use N1ebieski\IDir\Repositories\PriceRepo;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Traits\Filterable;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Models\Traits\Carbonable;

class Price extends Model
{
    use Filterable, Carbonable;

    // Configuration

    /**
     * [public description]
     * @var array
     */
    public const AVAILABLE = ['transfer', 'code_transfer', 'code_sms', 'paypal_express'];

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
        'token',
        'number'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['token'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'group_id' => 'integer',
        'price' => 'decimal:2',
        'days' => 'integer',
        'number' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
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
                $codes[] = $code->code . '|' . ($code->quantity !== null ? $code->quantity : 0);
            }

            return (string)implode("\r\n", $codes);
        }

        return $this->attributes['codes']['codes'] ?? null;
    }

    /**
     * [getCodesAttribute description]
     * @return Collect|null [description]
     */
    public function getCodesAttribute() : Collect
    {
        if ($this->relationLoaded('codes') && $this->getRelation('codes') instanceof Collection) {
            return $this->getRelation('codes');
        }

        return isset($this->attributes['codes']['codes']) ?
            Collect::make(explode("\r\n", $this->attributes['codes']['codes']))
            : Collect::make([]);
    }

    // Checkers

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isUnlimited() : bool
    {
        return $this->days === null;
    }

    // Makers

    /**
     * [makeService description]
     * @return PriceService [description]
     */
    public function makeService()
    {
        return App::make(PriceService::class, ['price' => $this]);
    }

    /**
     * [makeRepo description]
     * @return PriceRepo [description]
     */
    public function makeRepo()
    {
        return App::make(PriceRepo::class, ['price' => $this]);
    }
}
