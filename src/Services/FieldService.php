<?php

namespace N1ebieski\IDir\Services;

use N1ebieski\IDir\Models\Field\Field;
use N1ebieski\ICore\Services\Serviceable;
use Illuminate\Database\Eloquent\Model;

/**
 * [FieldService description]
 */
class FieldService implements Serviceable
{
    /**
     * Model
     * @var Field
     */
    protected $field;

    /**
     * [__construct description]
     * @param Field       $field       [description]
     */
    public function __construct(Field $field)
    {
        $this->field = $field;
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes) : Model
    {
        $this->field->fill($attributes);
        $this->field->options = array_merge($attributes[$attributes['type']], ['required' => $attributes['required']]);
        $this->field->save();

        $this->field->morphs()->attach($attributes['morphs']);

        return $this->field;
    }

    /**
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes) : bool
    {
        $this->field->fill($attributes);
        $this->field->options = array_merge($attributes[$attributes['type']], ['required' => $attributes['required']]);

        $this->field->morphs()->sync($attributes['morphs']);

        return $this->field->save();
    }

    /**
     * [updateStatus description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateStatus(array $attributes) : bool
    {

    }

    /**
     * [updatePosition description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updatePosition(array $attributes) : bool
    {
        return $this->field->update(['position' => (int)$attributes['position']]);
    }

    /**
     * [delete description]
     * @return bool [description]
     */
    public function delete() : bool
    {
        //
    }

    /**
     * [deleteGlobal description]
     * @param  array $ids [description]
     * @return int        [description]
     */
    public function deleteGlobal(array $ids) : int
    {

    }
}
