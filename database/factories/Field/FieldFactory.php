<?php

namespace N1ebieski\IDir\Database\Factories\Field;

use N1ebieski\IDir\Models\Field\Field;
use N1ebieski\IDir\ValueObjects\Field\Type;
use N1ebieski\IDir\ValueObjects\Field\Required;
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
                'type' => Type::INPUT,
                'options' => [
                    'min' => rand(3, 30),
                    'max' => rand(100, 300),
                    'required' => Required::ACTIVE
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
                'type' => Type::TEXTAREA,
                'options' => [
                    'min' => rand(3, 30),
                    'max' => rand(100, 3000),
                    'required' => Required::ACTIVE
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
                'type' => Type::SELECT,
                'options' => [
                    'options' => $this->faker->words(5, false),
                    'required' => Required::ACTIVE
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
                'type' => Type::MULTISELECT,
                'options' => [
                    'options' => $this->faker->words(5, false),
                    'required' => Required::ACTIVE
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
                'type' => Type::CHECKBOX,
                'options' => [
                    'options' => $this->faker->words(5, false),
                    'required' => Required::ACTIVE
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
                'type' => Type::IMAGE,
                'options' => [
                    'width' => 720,
                    'height' => 480,
                    'size' => 2048,
                    'required' => Required::ACTIVE
                ]
            ];
        });
    }
}
