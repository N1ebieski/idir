<?php

namespace N1ebieski\IDir\Casts\Group;

use N1ebieski\IDir\ValueObjects\Group\Visible;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class VisibleCast implements CastsAttributes
{
    /**
     * Transform the attribute from the underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return Visible
     */
    public function get($model, string $key, $value, array $attributes): Visible
    {
        return (!$value instanceof Visible) ? new Visible($value) : $value;
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return Visible
     */
    public function set($model, string $key, $value, array $attributes): Visible
    {
        if (is_string($value)) {
            $value = Visible::fromString($value);
        }

        if (is_int($value)) {
            $value = new Visible($value);
        }

        if (!$value instanceof Visible) {
            throw new \InvalidArgumentException('The given value is not a Visible instance');
        }

        return $value;
    }
}
