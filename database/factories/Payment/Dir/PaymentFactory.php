<?php

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
     * @var string
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
