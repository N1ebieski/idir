<?php

namespace N1ebieski\IDir\Events\Api\Payment\Dir;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use Illuminate\Broadcasting\InteractsWithSockets;

class StoreEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * [public description]
     * @var Payment
     */
    public $payment;

    /**
     * Create a new event instance.
     *
     * @param Payment         $payment    [description]
     * @return void
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }
}
