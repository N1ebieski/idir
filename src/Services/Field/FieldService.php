<?php

namespace N1ebieski\IDir\Services\Field;

use N1ebieski\IDir\Models\Field\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as Collect;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\IDir\Services\Field\Value\Types\Value;
use N1ebieski\IDir\Services\Field\Value\ValueFactory;
use N1ebieski\ICore\Services\Interfaces\CreateInterface;
use N1ebieski\ICore\Services\Interfaces\UpdateInterface;
use N1ebieski\IDir\Exceptions\Field\ValueNotFoundException;
use N1ebieski\ICore\Services\Interfaces\PositionUpdateInterface;

class FieldService implements CreateInterface, UpdateInterface, PositionUpdateInterface
{
    /**
     * Model
     * @var Field
     */
    protected $field;

    /**
     * Undocumented variable
     *
     * @var ValueFactory
     */
    protected $valueFactory;

    /**
     * Undocumented variable
     *
     * @var DB
     */
    protected $db;

    /**
     * Undocumented variable
     *
     * @var Collect
     */
    protected $collect;

    /**
     * Undocumented function
     *
     * @param Field $field
     * @param ValueFactory $valueFactory
     * @param DB $db
     * @param Collect $collect
     */
    public function __construct(
        Field $field,
        ValueFactory $valueFactory,
        DB $db,
        Collect $collect
    ) {
        $this->field = $field;

        $this->valueFactory = $valueFactory;

        $this->db = $db;
        $this->collect = $collect;
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
                        $this->makeValue($field)->delete($attributes[$field->id]);
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
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes): Model
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->field->fill($attributes);

            $this->field->options = array_merge(
                $attributes[$attributes['type']],
                ['required' => $attributes['required']]
            );

            $this->field->save();

            if (array_key_exists('morphs', $attributes)) {
                $this->field->morphs()->attach($attributes['morphs'] ?? []);
            }

            return $this->field;
        });
    }

    /**
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes): bool
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->field->fill($attributes);

            if (array_key_exists('required', $attributes)) {
                $this->field->options->setRequired($attributes['required']);
            }

            if (array_key_exists('type', $attributes) && array_key_exists($attributes['type'], $attributes)) {
                $options = $attributes[$attributes['type']];

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
                    $this->field->options->setOptions($options['height']);
                }

                if (array_key_exists('width', $options)) {
                    $this->field->options->setOptions($options['width']);
                }

                if (array_key_exists('size', $options)) {
                    $this->field->options->setOptions($options['size']);
                }
            }

            if (array_key_exists('morphs', $attributes)) {
                $this->field->morphs()->sync($attributes['morphs'] ?? []);
            }

            return $this->field->save();
        });
    }

    /**
     * [updatePosition description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updatePosition(array $attributes): bool
    {
        return $this->db->transaction(function () use ($attributes) {
            return $this->field->update(['position' => (int)$attributes['position']]);
        });
    }

    /**
     * Undocumented function
     *
     * @param Field $field
     * @return Value
     */
    protected function makeValue(Field $field): Value
    {
        return $this->valueFactory->makeValue(
            (clone $this->field)->setRawAttributes($field->getAttributes())
        );
    }
}
