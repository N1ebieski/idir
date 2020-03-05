<?php

namespace N1ebieski\IDir\Http\Requests\Web\Payment\Cashbill;

use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\IDir\Http\Requests\Web\Payment\Interfaces\ShowRequestStrategy;

/**
 * [ShowRequest description]
 */
class ShowRequest extends FormRequest implements ShowRequestStrategy
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'redirect' => [
                'bail',
                'nullable',
                'string',
                'regex:/^(https|http):\/\/([\da-z\.-]+)(\.[a-z]{2,6})/'
            ]
        ];
    }
}
