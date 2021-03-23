<?php

namespace N1ebieski\IDir\Services\Payment;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Models\Payment\Payment;
use N1ebieski\ICore\Services\Interfaces\Creatable;
use Illuminate\Contracts\Config\Repository as Config;
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
     * Undocumented variable
     *
     * @var Config
     */
    protected $config;

    /**
     * Undocumented function
     *
     * @param Payment $payment
     * @param Carbon $carbon
     * @param Config $config
     */
    public function __construct(Payment $payment, Carbon $carbon, Config $config)
    {
        $this->payment = $payment;

        $this->carbon = $carbon;
        $this->config = $config;
    }

    /**
     * [makeStatus description]
     * @param  string|null  $payment_type  [description]
     * @return int [description]
     */
    protected function makeStatus(string $payment_type = null) : int
    {
        if (in_array($payment_type, ['transfer', 'paypal_express'])) {
            return Payment::PENDING;
        }

        return Payment::UNFINISHED;
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes) : Model
    {
        $this->payment->status = $this->makeStatus($attributes['payment_type'] ?? null);
        $this->payment->driver = $this->config->get("idir.payment.{$attributes['payment_type']}.driver");
        $this->payment->morph()->associate($this->payment->morph);
        $this->payment->orderMorph()->associate($this->payment->order);
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
