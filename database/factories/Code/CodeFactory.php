<?php

namespace N1ebieski\IDir\Database\Factories\Code;

use N1ebieski\IDir\Models\Code;
use Illuminate\Database\Eloquent\Factories\Factory;

class CodeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Code::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->word,
            'quantity' => rand(0, 20),
        ];
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function one()
    {
        return $this->state(function () {
            return [
                'quantity' => 1
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function two()
    {
        return $this->state(function () {
            return [
                'quantity' => 2
            ];
        });
    }
}
