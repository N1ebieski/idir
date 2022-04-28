<?php

namespace N1ebieski\IDir\Casts\BanValue;

use N1ebieski\IDir\ValueObjects\BanValue\Type;
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
