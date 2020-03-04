<?php

namespace N1ebieski\IDir\Services;

use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Models\Payment\Payment;
use Illuminate\Support\Carbon;
use N1ebieski\ICore\Services\Interfaces\Creatable;
use N1ebieski\ICore\Services\Interfaces\StatusUpdatable;

/**
 * [PaymentService description]
 */
class PaymentService implements Creatable, StatusUpdatable
{
    /**
     * Model
     * @var Payment
     */
    protected $payment;

    /**
     * Undocumented variable
     *
     * @var Carbon
     */
    protected $carbon;

    /**
     * Undocumented function
     *
     * @param Payment $payment
     * @param Carbon $carbon
     */
    public function __construct(Payment $payment, Carbon $carbon)
    {
        $this->payment = $payment;

        $this->carbon = $carbon;
    }

    /**
     * [makeStatus description]
     * @param  string|null  $payment_type  [description]
     * @return int [description]
     */
    protected function makeStatus(string $payment_type = null) : int
    {
        if ($payment_type === 'transfer') {
            return 2;
        }

        return 0;
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes) : Model
    {
        $this->payment->status = $this->makeStatus($attributes['payment_type'] ?? null);
        $this->payment->morph()->associate($this->payment->getMorph());
        $this->payment->priceMorph()->associate($this->payment->getPriceMorph());
        $this->payment->save();

        return $this->payment;
    }

    /**
     * Update Status attribute the specified Payment in storage.
     *
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateStatus(array $attributes) : bool
    {
        return $this->payment->update(['status' => $attributes['status']]);
    }

    /**
     * Update Logs attribute the specified Payment in storage.
     *
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateLogs(array $attributes) : bool
    {
        return $this->payment->update([
            'logs' => $this->payment->logs . "\r\n" . $this->carbon->now() . "\r\n" . $attributes['logs']
        ]);
    }
}
