<?php

namespace N1ebieski\IDir\Http\Resources\Field\Dir;

use N1ebieski\IDir\Models\Field\Dir\Field;
use N1ebieski\IDir\Http\Resources\Field\FieldResource as BaseFieldResource;

class FieldResource extends BaseFieldResource
{
    /**
     * Undocumented function
     *
     * @param Field $field
     */
    public function __construct(Field $field)
    {
        parent::__construct($field);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'value' => $this->decode_value
        ]);
    }
}
