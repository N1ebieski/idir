<?php

namespace N1ebieski\IDir\Http\Requests\Web\Payment\Cashbill;

use Illuminate\Foundation\Http\FormRequest;

class CompleteRequest extends FormRequest
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

    // /**
    //  * Prepare the data for validation.
    //  *
    //  * @return void
    //  */
    // public function prepareForValidation() : void
    // {
    //     if ($this->has('amount')) {
    //         $this->merge(['amount' => number_format((float)$this->input('amount'), 2)]);
    //     }
    // }

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
