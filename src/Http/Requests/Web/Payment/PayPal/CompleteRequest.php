<?php

namespace N1ebieski\IDir\Http\Requests\Web\Payment\PayPal;

use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\IDir\Http\Requests\Web\Payment\Interfaces\CompleteRequestInterface;

class CompleteRequest extends FormRequest implements CompleteRequestInterface
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'token' => 'bail|required|string',
            'PayerID' => 'bail|nullable|string',
            'status' => 'bail|required|in:ok,err',
            'uuid' => 'bail|required|uuid',
            'redirect' => [
                'bail',
                'nullable',
                'string',
                'regex:/^(https|http):\/\/([\da-z\.-]+)(\.[a-z]{2,6})/'
            ]
        ];
    }
}
