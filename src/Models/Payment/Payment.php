<?php

namespace N1ebieski\IDir\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Services\PaymentService;

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

    // Loads

    /**
     * [loadCheckoutPayments description]
     * @return self [description]
     */
    public function loadCheckoutPayments() : self
    {
        return $this->load(['payments' => function($query) {
            $query->with('price_morph')->where('status', 0);
        }]);
    }

    // Getters

    /**
     * [getService description]
     * @return PaymentService [description]
     */
    public function getService() : PaymentService
    {
        return app()->make(PaymentService::class, ['payment' => $this]);
    }
}
