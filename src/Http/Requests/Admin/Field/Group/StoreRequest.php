<?php

namespace N1ebieski\IDir\Http\Requests\Admin\Field\Group;

use N1ebieski\IDir\Http\Requests\Admin\Field\StoreRequest as BaseStoreRequest;

class StoreRequest extends BaseStoreRequest
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
                'nullable',
                'array',
                'exists:groups,id'
            ]
        ], parent::rules());
    }
}
