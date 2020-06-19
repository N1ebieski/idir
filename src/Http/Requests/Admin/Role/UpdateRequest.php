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
                        'web.*',
                        'web.comments.*',
                        'web.comments.create',
                        'web.comments.suggest',
                        'web.comments.edit',
                        'web.dirs.*',
                        'web.dirs.create',
                        'web.dirs.edit',
                        'web.dirs.delete',
                        'web.dirs.notification'
                    ])
                    : null,
                'no_js_validation'
            ]
        ]);
    }
}
