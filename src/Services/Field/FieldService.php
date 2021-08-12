<?php

namespace N1ebieski\IDir\Services\Field;

use N1ebieski\IDir\Models\Field\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as Collect;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\Services\Interfaces\Creatable;
use N1ebieski\ICore\Services\Interfaces\Updatable;
use N1ebieski\IDir\Services\Field\Value\Types\Value;
use N1ebieski\IDir\Services\Field\Value\ValueFactory;
use N1ebieski\ICore\Services\Interfaces\PositionUpdatable;

class FieldService implements Creatable, Updatable, PositionUpdatable
{
    /**
     * @var array
     */
    protected const SPECIAL = ['image', 'map', 'regions'];

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
    public function prepareFieldAttribute(array $attributes): array
    {
        if (array_key_exists('field', $attributes)) {
            foreach ($this->field->morph->group->fields()->get() as $field) {
                if (!empty($attributes['field'][$field->id])) {
                    $value = $attributes['field'][$field->id];

                    if (in_array($field->type, static::SPECIAL)) {
                        $attributes['field'][$field->id] = $this->makeValue($field)->prepare($value);
                    }
                }
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

            foreach ($this->field->morph->group->fields()->get() as $field) {
                if (array_key_exists($field->id, $attributes)) {
                    if (!empty($attributes[$field->id])) {
                        $value = $attributes[$field->id];

                        if (in_array($field->type, static::SPECIAL)) {
                            $value = $this->makeValue($field)->create($value);
                        }

                        $ids[$field->id] = ['value' => json_encode($value)];
                        $i++;
                    }
                }
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

            foreach ($this->field->morph->group->fields()->get() as $field) {
                if (array_key_exists($field->id, $attributes)) {
                    if (!empty($attributes[$field->id])) {
                        $value = $attributes[$field->id];

                        if (in_array($field->type, static::SPECIAL)) {
                            $value = $this->makeValue($field)->update($value);
                        }

                        $ids[$field->id] = ['value' => json_encode($value)];
                        $i++;
                    } else {
                        if (in_array($field->type, static::SPECIAL)) {
                            $this->makeValue($field)->delete();
                        }
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

            $this->field->options = array_merge(
                ['required' => $attributes['required']],
                isset($attributes['type']) ?
                    $attributes[$attributes['type']] : []
            );

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
