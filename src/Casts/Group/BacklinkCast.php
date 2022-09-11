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

namespace N1ebieski\IDir\Casts\Group;

use N1ebieski\IDir\ValueObjects\Group\Backlink;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class BacklinkCast implements CastsAttributes
{
    /**
     * Transform the attribute from the underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return Backlink
     */
    public function get($model, string $key, $value, array $attributes): Backlink
    {
        return (!$value instanceof Backlink) ? new Backlink($value) : $value;
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return Backlink
     */
    public function set($model, string $key, $value, array $attributes): Backlink
    {
        if (is_string($value)) {
            $value = Backlink::fromString($value);
        }

        if (is_int($value)) {
            $value = new Backlink($value);
        }

        if (!$value instanceof Backlink) {
            throw new \InvalidArgumentException('The given value is not a Backlink instance');
        }

        return $value;
    }
}
