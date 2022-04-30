<?php

namespace N1ebieski\IDir\Services\Payment;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Models\Payment\Payment;
use N1ebieski\IDir\ValueObjects\Price\Type;
use N1ebieski\IDir\ValueObjects\Payment\Status;
use N1ebieski\ICore\Services\Interfaces\Creatable;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\ICore\Services\Interfaces\StatusUpdatable;

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
        $this->setPayment($payment);

        $this->carbon = $carbon;
        $this->config = $config;
    }

    /**
     * Undocumented function
     *
     * @param Payment $payment
     * @return static
     */
    public function setPayment(Payment $payment)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes): Model
    {
        try {
            $this->payment->status = Status::fromString(
                $attributes['payment_type'] ?? Status::UNFINISHED
            );
        } catch (\InvalidArgumentException $e) {
            $this->payment->status = Status::UNFINISHED;
        }

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
    public function updateStatus(array $attributes): bool
    {
        return $this->payment->update(['status' => $attributes['status']]);
    }

    /**
     * Update Logs attribute the specified Payment in storage.
     *
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateLogs(array $attributes): bool
    {
        return $this->payment->update([
            'logs' => $this->payment->logs . "\r\n" . $this->carbon->now() . "\r\n" . $attributes['logs']
        ]);
    }
}
