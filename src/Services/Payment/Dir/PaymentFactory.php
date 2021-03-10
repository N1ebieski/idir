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
     * Undocumented variable
     *
     * @var Dir
     */
    protected $dir;

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $paymentType;

    /**
     * Undocumented function
     *
     * @param Payment $payment
     * @param Price $price
     * @param Dir $dir
     * @param integer $priceId
     * @param string $paymentType
     */
    public function __construct(
        Payment $payment,
        Price $price,
        Dir $dir,
        int $priceId,
        string $paymentType
    ) {
        $this->payment = $payment;
        $this->price = $price->find($priceId);
        $this->dir = $dir;

        $this->paymentType = $paymentType;
    }

    /**
     * Undocumented function
     *
     * @return Payment
     */
    public function makePayment() : Payment
    {
        return $this->payment->setRelations([
                'morph' => $this->dir,
                'order' => $this->price
            ])
            ->makeService()
            ->create([
                'payment_type' => $this->paymentType
            ]);
    }
}
