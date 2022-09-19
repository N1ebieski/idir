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

namespace N1ebieski\IDir\Database\Factories\Field;

use N1ebieski\IDir\Models\Field\Field;
use N1ebieski\IDir\ValueObjects\Field\Type;
use N1ebieski\IDir\ValueObjects\Field\Visible;
use N1ebieski\IDir\ValueObjects\Field\Required;
use Illuminate\Database\Eloquent\Factories\Factory;

class FieldFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Field>
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
            'visible' => rand(Visible::INACTIVE, Visible::ACTIVE)
        ];
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public function public(): self
    {
        return $this->state(function () {
            return [
                'visible' => Visible::ACTIVE
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public function private(): self
    {
        return $this->state(function () {
            return [
                'visible' => Visible::INACTIVE
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public function input(): self
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
    * @return self
    */
    public function textarea(): self
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
    * @return self
    */
    public function select(): self
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
    * @return self
    */
    public function multiselect(): self
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
    * @return self
    */
    public function checkbox(): self
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

    public function switch(): self
    {
        return $this->state(function () {
            return [
                'type' => Type::SWITCH,
                'options' => [
                    'required' => Required::ACTIVE
                ]
            ];
        });
    }

    /**
    * Undocumented function
    *
    * @return self
    */
    public function image(): self
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
