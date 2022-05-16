<?php

namespace N1ebieski\IDir\Http\Requests\Admin\Field\Group;

use Illuminate\Support\Collection as Collect;
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
                'nullable',
                'array',
                'exists:groups,id'
            ]
        ], parent::rules());
    }

    /**
     * Get the validated data from the request.
     *
     * @return array
     */
    public function validated()
    {
        return Collect::make(
            $this->safe()->only(['title', 'type', 'visible', 'desc', 'morphs'])
        )
        ->merge([
            'options' => array_merge(
                $this->safe()->collect()->get($this->safe()->type, []),
                $this->safe()->only('required')
            )
        ])
        ->toArray();
    }
}
