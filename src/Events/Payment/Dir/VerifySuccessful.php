<?php

namespace N1ebieski\IDir\Events\Payment\Dir;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Models\Dir;

/**
 * [PaymentVerify description]
 */
class VerifySuccessful
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * [public description]
     * @var Payment
     */
    public $payment;

    /**
     * [public description]
     * @var Dir
     */
    public $dir;

    /**
     * Create a new event instance.
     *
     * @param Payment         $payment    [description]
     * @return void
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
        $this->dir = $payment->morph;
    }
}
