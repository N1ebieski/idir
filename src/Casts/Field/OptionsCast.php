<?php

namespace N1ebieski\IDir\Casts\Field;

use N1ebieski\IDir\ValueObjects\Field\Options;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class OptionsCast implements CastsAttributes
{
    /**
     * Transform the attribute from the underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return Options
     */
    public function get($model, string $key, $value, array $attributes): Options
    {
        return (!$value instanceof Options) ? new Options(json_decode($value)) : $value;
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return Options
     */
    public function set($model, string $key, $value, array $attributes): Options
    {
        if (is_array($value)) {
            $value = new Options((object)$value);
        }

        if (!$value instanceof Options) {
            throw new \InvalidArgumentException('The given value is not a Options instance');
        }

        return $value;
    }
}
