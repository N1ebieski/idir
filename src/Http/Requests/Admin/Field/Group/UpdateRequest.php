<?php

namespace N1ebieski\IDir\Http\Requests\Admin\Field\Group;

use N1ebieski\IDir\Http\Requests\Admin\Field\UpdateRequest as BaseUpdateRequest;

class UpdateRequest extends BaseUpdateRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge([
            'morphs' => [
                'bail',
                'required',
                'array',
                'exists:groups,id'
            ]
        ], parent::rules());
    }
}
