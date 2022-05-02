<?php

namespace N1ebieski\IDir\Casts\Group;

use N1ebieski\IDir\ValueObjects\Group\Slug;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class SlugCast implements CastsAttributes
{
    /**
     * Transform the attribute from the underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return Slug|null
     */
    public function get($model, string $key, $value, array $attributes): ?Slug
    {
        return ($value !== null && !($value instanceof Slug)) ? new Slug($value) : $value;
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return Slug
     */
    public function set($model, string $key, $value, array $attributes): Slug
    {
        if (is_string($value)) {
            $value = new Slug($value);
        }

        if (!$value instanceof Slug) {
            throw new \InvalidArgumentException('The given value is not a Slug instance');
        }

        return $value;
    }
}
