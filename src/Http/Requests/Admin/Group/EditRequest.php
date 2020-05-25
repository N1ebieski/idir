<?php

namespace N1ebieski\IDir\Http\Requests\Admin\Group;

use Illuminate\Foundation\Http\FormRequest;

/**
 * [EditRequest description]
 */
class EditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->group->isNotDefault();
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
