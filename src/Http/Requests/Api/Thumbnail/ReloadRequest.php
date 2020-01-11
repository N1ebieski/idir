<?php

namespace N1ebieski\IDir\Http\Requests\Api\Thumbnail;

use Illuminate\Foundation\Http\FormRequest;

class ReloadRequest extends FormRequest
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
            'url' => [
                'bail',
                'required',
                'string',
                'regex:/^(https|http):\/\/([\da-z\.-]+)(\.[a-z]{2,6})\/?$/'
            ]
        ];
    }
}
