<?php

namespace N1ebieski\IDir\Casts\Group;

use N1ebieski\IDir\ValueObjects\Group\Url;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class UrlCast implements CastsAttributes
{
    /**
     * Transform the attribute from the underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return Url
     */
    public function get($model, string $key, $value, array $attributes): Url
    {
        return (!$value instanceof Url) ? new Url($value) : $value;
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return Url
     */
    public function set($model, string $key, $value, array $attributes): Url
    {
        if (is_string($value)) {
            $value = Url::fromString($value);
        }

        if (is_int($value)) {
            $value = new Url($value);
        }

        if (!$value instanceof Url) {
            throw new \InvalidArgumentException('The given value is not a Url instance');
        }

        return $value;
    }
}