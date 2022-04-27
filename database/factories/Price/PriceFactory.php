<?php

namespace N1ebieski\IDir\Database\Factories\Price;

use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Price;
use N1ebieski\IDir\ValueObjects\Price\Type;
use Illuminate\Database\Eloquent\Factories\Factory;

class PriceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Price::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(Type::getAvailable()),
            'price' => number_format(rand(12, 57) / 10, 2),
            'days' => $this->faker->randomElement([rand(7, 365), null]),
        ];
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function transfer()
    {
        return $this->state(function () {
            return [
                'type' => Type::TRANSFER
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function codeSms()
    {
        return $this->state(function () {
            return [
                'type' => Type::CODE_SMS,
                'number' => 99999,
                'code' => 'XX.XXX',
                'token' => 'c78zs8ds8ds'
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function codeTransfer()
    {
        return $this->state(function () {
            return [
                'type' => Type::CODE_TRANSFER,
                'code' => 'dasdasdasd'
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function seasonal()
    {
        return $this->state(function () {
            return [
                'days' => rand(7, 365)
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function withGroup()
    {
        return $this->for(Group::makeFactory()->public());
    }
}
