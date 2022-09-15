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

namespace N1ebieski\IDir\Database\Factories\Payment\Dir;

use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Price;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Database\Factories\Payment\PaymentFactory as BasePaymentFactory;

class PaymentFactory extends BasePaymentFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Payment>
     */
    protected $model = Payment::class;

    /**
     * Undocumented function
     *
     * @return static
     */
    public function withMorph()
    {
        return $this->for(
            Dir::makeFactory()->titleSentence()->contentText()->pending()->withUser()->withCategory()->withDefaultGroup(),
            'morph'
        );
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function withOrder()
    {
        return $this->for(
            Price::makeFactory()->transfer()->withGroup(),
            'orderMorph'
        );
    }
}
