<?php

namespace N1ebieski\IDir\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Services\PaymentService;
use N1ebieski\IDir\Repositories\PaymentRepo;
use Illuminate\Database\Eloquent\Builder;

/**
 * [Payment description]
 */
class Payment extends Model
{
    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'logs'
    ];

    // Relations

    /**
     * [user description]
     * @return [type] [description]
     */
    public function user()
    {
        return $this->belongsTo('N1ebieski\ICore\Models\User');
    }

    /**
     * [morph description]
     * @return [type] [description]
     */
    public function morph()
    {
        return $this->morphTo('model');
    }

    /**
     * [priceMorph description]
     * @return [type] [description]
     */
    public function price_morph()
    {
        return $this->morphTo('price');
    }

    // Scopes

    /**
     * [scopePublic description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopePending(Builder $query) : Builder
    {
        return $query->where('status', 2);
    }

    // Makers

    /**
     * [makeService description]
     * @return PaymentService [description]
     */
    public function makeService() : PaymentService
    {
        return app()->make(PaymentService::class, ['payment' => $this]);
    }

    /**
     * [makeRepo description]
     * @return PaymentRepo [description]
     */
    public function makeRepo() : PaymentRepo
    {
        return app()->make(PaymentRepo::class, ['payment' => $this]);
    }
}
