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

namespace N1ebieski\IDir\Services\Field;

use Throwable;
use N1ebieski\IDir\Models\Field\Field;
use Illuminate\Support\Collection as Collect;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\IDir\Services\Field\Value\ValueFactory;
use N1ebieski\IDir\Exceptions\Field\ValueNotFoundException;
use Illuminate\Contracts\Container\BindingResolutionException;
use N1ebieski\IDir\Services\Field\Value\Types\Interfaces\FileInterface;
use N1ebieski\IDir\Services\Field\Value\Types\Interfaces\ArrayInterface;

class FieldService
{
    /**
     * Undocumented function
     *
     * @param Field $field
     * @param ValueFactory $valueFactory
     * @param DB $db
     * @param Collect $collect
     */
    public function __construct(
        protected Field $field,
        protected ValueFactory $valueFactory,
        protected DB $db,
        protected Collect $collect
    ) {
        //
    }

    /**
     * [prepareField description]
     * @param  array $attributes [description]
     * @return array             [description]
     */
    public function prepareValues(array $attributes): array
    {
        foreach ($this->field->all() as $field) {
            if (!array_key_exists($field->id, $attributes)) {
                continue;
            }

            if (empty($attributes[$field->id])) {
                continue;
            }

            try {
                $attributes[$field->id] = $this->makeValue($field)->prepare($attributes[$field->id]);
            } catch (ValueNotFoundException $e) {
                continue;
            }
        }

        return $attributes;
    }

    /**
     * [createValues description]
     * @param  array $attributes [description]
     * @return int               [description]
     */
    public function createValues(array $attributes): int
    {
        return $this->db->transaction(function () use ($attributes) {
            $i = 0;

            foreach ($this->field->all() as $field) {
                if (!array_key_exists($field->id, $attributes)) {
                    continue;
                }

                if (empty($attributes[$field->id])) {
                    continue;
                }

                try {
                    $attributes[$field->id] = $this->makeValue($field)->create($attributes[$field->id]);
                } catch (ValueNotFoundException $e) {
                    //
                }

                $ids[$field->id] = ['value' => json_encode($attributes[$field->id])];
                $i++;
            }

            $this->field->morph->fields()->attach($ids ?? []);

            return $i;
        });
    }

    /**
     * [updateValues description]
     * @param  array $attributes [description]
     * @return int               [description]
     */
    public function updateValues(array $attributes): int
    {
        return $this->db->transaction(function () use ($attributes) {
            $i = 0;

            foreach ($this->field->all() as $field) {
                if (!array_key_exists($field->id, $attributes)) {
                    continue;
                }

                if (!empty($attributes[$field->id])) {
                    try {
                        $attributes[$field->id] = $this->makeValue($field)->update($attributes[$field->id]);
                    } catch (ValueNotFoundException $e) {
                        //
                    }

                    $ids[$field->id] = ['value' => json_encode($attributes[$field->id])];
                    $i++;
                } else {
                    try {
                        $this->makeValue($field)->delete();
                    } catch (ValueNotFoundException $e) {
                        //
                    }
                }
            }

            $this->field->morph->fields()->syncWithoutDetaching($ids ?? []);

            $this->field->morph->fields()->detach(
                $this->collect->make($attributes)
                    ->filter(function ($value) {
                        return empty($value);
                    })
                    ->keys()
                    ->toArray()
            );

            return $i;
        });
    }

    /**
     *
     * @param array $attributes
     * @return Field
     * @throws Throwable
     */
    public function create(array $attributes): Field
    {
        return $this->db->transaction(function () use ($attributes) {
            $field = $this->field->fill($attributes);

            $field->save();

            if (array_key_exists('morphs', $attributes)) {
                $field->morphs()->attach($attributes['morphs'] ?? []);
            }

            return $field;
        });
    }

    /**
     *
     * @param array $attributes
     * @return Field
     * @throws Throwable
     */
    public function update(array $attributes): Field
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->field->fill(
                $this->collect->make($attributes)->except('options')->toArray()
            );

            if (array_key_exists('options', $attributes)) {
                $options = $attributes['options'];

                if (array_key_exists('required', $options)) {
                    $this->field->options->setRequired($options['required']);
                }

                if (array_key_exists('options', $options)) {
                    $this->field->options->setOptions($options['options']);
                }

                if (array_key_exists('min', $options)) {
                    $this->field->options->setMin($options['min']);
                }

                if (array_key_exists('max', $options)) {
                    $this->field->options->setMax($options['max']);
                }

                if (array_key_exists('height', $options)) {
                    $this->field->options->setHeight($options['height']);
                }

                if (array_key_exists('width', $options)) {
                    $this->field->options->setWidth($options['width']);
                }

                if (array_key_exists('size', $options)) {
                    $this->field->options->setSize($options['size']);
                }
            }

            if (array_key_exists('morphs', $attributes)) {
                $this->field->morphs()->sync($attributes['morphs'] ?? []);
            }

            $this->field->save();

            return $this->field;
        });
    }

    /**
     *
     * @param int $position
     * @return bool
     * @throws Throwable
     */
    public function updatePosition(int $position): bool
    {
        return $this->db->transaction(function () use ($position) {
            return $this->field->update(['position' => $position]);
        });
    }

    /**
     *
     * @param Field $field
     * @return ArrayInterface|FileInterface
     * @throws BindingResolutionException
     * @throws ValueNotFoundException
     */
    protected function makeValue(Field $field): ArrayInterface|FileInterface
    {
        return $this->valueFactory->makeValue(
            (clone $this->field)->setRawAttributes($field->getAttributes())
        );
    }
}
