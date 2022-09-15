<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

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
     * Undocumented function
     *
     * @param Payment $payment
     * @param Price $price
     */
    public function __construct(
        protected Payment $payment,
        protected Price $price
    ) {
        //
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
        /** @var Price */
        $price = $this->price->find($priceId);

        /** @var Payment */
        $payment = $this->payment->makeService()->create([
            'morph' => $dir,
            'order' => $price,
            'payment_type' => $price->type->getValue()
        ]);

        return $payment;
    }
}
