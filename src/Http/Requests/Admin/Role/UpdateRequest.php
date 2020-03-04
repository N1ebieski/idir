<?php

namespace N1ebieski\IDir\Http\Requests\Admin\Role;

use N1ebieski\ICore\Http\Requests\Admin\Role\UpdateRequest as BaseUpdateRequest;
use Illuminate\Validation\Rule;

/**
 * [UpdateRequest description]
 */
class UpdateRequest extends BaseUpdateRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            'perm.*' => [
                'bail',
                'nullable',
                'string',
                'distinct',
                'exists:permissions,name',
                $this->role->name === 'user' ?
                    Rule::in([
                        'create comments',
                        'suggest comments',
                        'edit comments',
                        'create dirs',
                        'edit dirs',
                        'destroy dirs',
                        'notification dirs'
                    ])
                    : null,
                'no_js_validation'
            ]
        ]);
    }
}
