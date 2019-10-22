<?php

namespace N1ebieski\IDir\Http\Requests\Web\Payment\Cashbill;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class VerifyRequest extends FormRequest
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

    public function prepareForValidation() {
        app(Request::class)->merge([
            'logs' => $this->all()
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'service' => 'bail|required|string|in:' . config("services.cashbill.transfer.service"),
            'orderid' => 'bail|required|string',
            'amount' => 'bail|required|numeric|between:0,9999.99',
            'userdata' => 'bail|required|integer',
            'status' => 'bail|required|in:ok,err',
            'sign' => 'bail|required|string'
        ];
    }
}
