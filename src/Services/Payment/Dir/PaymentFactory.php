<?php

namespace N1ebieski\IDir\Services\Payment\Dir;

use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Price;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use Illuminate\Database\ClassMorphViolationException;
use Illuminate\Database\Eloquent\InvalidCastException;
use Illuminate\Database\Eloquent\JsonEncodingException;

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
     *
     * @param Dir $dir
     * @param int $priceId
     * @return Payment
     * @throws InvalidCastException
     * @throws JsonEncodingException
     * @throws ClassMorphViolationException
     */
    public function makePayment(Dir $dir, int $priceId): Payment
    {
        /**
         * @var Price
         */
        $price = $this->price->find($priceId);

        return $this->payment->setRelations([
                'morph' => $dir,
                'order' => $price
            ])
            ->makeService()
            ->create([
                'payment_type' => $price->type->getValue()
            ]);
    }
}
