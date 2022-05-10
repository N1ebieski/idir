<?php

namespace N1ebieski\IDir\Http\Requests\Api\Dir;

use N1ebieski\IDir\Models\Group;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\IDir\Http\Requests\Traits\HasCodes;

/**
 * @property Group $group
 */
class StoreCodeRequest extends FormRequest
{
    use HasCodes;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->group->isAvailable()
            && ($this->group->visible->isActive() || optional($this->user())->can('admin.dirs.create'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->prepareCodeRules();
    }
}
