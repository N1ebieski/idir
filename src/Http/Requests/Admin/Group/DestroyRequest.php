<?php

namespace N1ebieski\IDir\Http\Requests\Admin\Group;

use N1ebieski\IDir\Models\Group;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property Group $group
 */
class DestroyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return !$this->group->slug->isDefault();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
