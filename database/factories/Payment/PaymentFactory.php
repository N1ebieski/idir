<?php

namespace N1ebieski\IDir\Database\Factories\Payment;

use N1ebieski\IDir\Models\Payment\Payment;
use N1ebieski\IDir\ValueObjects\Payment\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            //
        ];
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function pending()
    {
        return $this->state(function () {
            return [
                'status' => Status::PENDING
            ];
        });
    }
}
