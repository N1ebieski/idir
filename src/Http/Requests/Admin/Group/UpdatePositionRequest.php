<?php

namespace N1ebieski\IDir\Http\Requests\Admin\Group;

use Illuminate\Foundation\Http\FormRequest;

/**
 * [UpdatePositionRequest description]
 */
class UpdatePositionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'position' => 'required|integer|between:0,' . ($this->group->countSiblings() - 1)
        ];
    }
}
