<?php

namespace N1ebieski\IDir\Database\Factories\Field;

use N1ebieski\IDir\Models\Field\Field;
use Illuminate\Database\Eloquent\Factories\Factory;

class FieldFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Field::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'title' => ucfirst($this->faker->unique()->word),
            'desc' => $this->faker->text(300),
            'visible' => rand(Field::INVISIBLE, Field::VISIBLE)
        ];
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function public()
    {
        return $this->state(function () {
            return [
                'visible' => Field::VISIBLE
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function private()
    {
        return $this->state(function () {
            return [
                'visible' => Field::INVISIBLE
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function input()
    {
        return $this->state(function () {
            return [
                'type' => 'input',
                'options' => [
                    'min' => rand(3, 30),
                    'max' => rand(100, 300),
                    'required' => Field::REQUIRED
                ]
            ];
        });
    }

    /**
    * Undocumented function
    *
    * @return static
    */
    public function textarea()
    {
        return $this->state(function () {
            return [
                'type' => 'textarea',
                'options' => [
                    'min' => rand(3, 30),
                    'max' => rand(100, 3000),
                    'required' => Field::REQUIRED
                ]
            ];
        });
    }

    /**
    * Undocumented function
    *
    * @return static
    */
    public function select()
    {
        return $this->state(function () {
            return [
                'type' => 'select',
                'options' => [
                    'options' => $this->faker->words(5, false),
                    'required' => Field::REQUIRED
                ]
            ];
        });
    }

    /**
    * Undocumented function
    *
    * @return static
    */
    public function multiselect()
    {
        return $this->state(function () {
            return [
                'type' => 'multiselect',
                'options' => [
                    'options' => $this->faker->words(5, false),
                    'required' => Field::REQUIRED
                ]
            ];
        });
    }

    /**
    * Undocumented function
    *
    * @return static
    */
    public function checkbox()
    {
        return $this->state(function () {
            return [
                'type' => 'checkbox',
                'options' => [
                    'options' => $this->faker->words(5, false),
                    'required' => Field::REQUIRED
                ]
            ];
        });
    }

    /**
    * Undocumented function
    *
    * @return static
    */
    public function image()
    {
        return $this->state(function () {
            return [
                'type' => 'image',
                'options' => [
                    'width' => 720,
                    'height' => 480,
                    'size' => 2048,
                    'required' => Field::REQUIRED
                ]
            ];
        });
    }
}
