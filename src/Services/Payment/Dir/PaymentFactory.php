<?php

namespace N1ebieski\IDir\Services\Payment\Dir;

use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Price;
use N1ebieski\IDir\Models\Payment\Dir\Payment;

class PaymentFactory
{
    /**
     * Undocumented variable
     *
     * @var Payment
     */
    protected $payment;

    /**
     * Undocumented variable
     *
     * @var Price
     */
    protected $price;

    /**
     * Undocumented function
     *
     * @param Payment $payment
     * @param Price $price
     */
    public function __construct(Payment $payment, Price $price)
    {
        $this->payment = $payment;
        $this->price = $price;
    }

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param integer $priceId
     * @param string $paymentType
     * @return Payment
     */
    public function makePayment(Dir $dir, int $priceId, string $paymentType): Payment
    {
        return $this->payment->setRelations([
                'morph' => $dir,
                'order' => $this->price->find($priceId)
            ])
            ->makeService()
            ->create([
                'payment_type' => $paymentType
            ]);
    }
}
