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

namespace N1ebieski\IDir\Casts\Price;

use N1ebieski\IDir\ValueObjects\Price\Type;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class TypeCast implements CastsAttributes
{
    /**
     * Transform the attribute from the underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return Type
     */
    public function get($model, string $key, $value, array $attributes): Type
    {
        return (!$value instanceof Type) ? new Type($value) : $value;
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return Type
     */
    public function set($model, string $key, $value, array $attributes): Type
    {
        if (is_string($value)) {
            $value = new Type($value);
        }

        if (!$value instanceof Type) {
            throw new \InvalidArgumentException('The given value is not a Type instance');
        }

        return $value;
    }
}
