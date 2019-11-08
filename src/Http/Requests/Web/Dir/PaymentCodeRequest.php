<?php

namespace N1ebieski\IDir\Http\Requests\Web\Dir;

use Illuminate\Foundation\Http\FormRequest;

class PaymentCodeRequest extends FormRequest
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
            'code_sms' => $this->input('payment_type') === 'code_sms' ?
            [
                'bail',
                'nullable',
                'required_if:payment_type,code_sms',
                'string',
                app()->make('N1ebieski\\IDir\\Rules\\Codes\\' . ucfirst(config('idir.payment.code_sms.driver')) . '\\SMS')
            ] : [],
            'code_transfer' => $this->input('payment_type') === 'code_transfer' ?
            [
                'bail',
                'nullable',
                'required_if:payment_type,code_transfer',
                'string',
                app()->make('N1ebieski\\IDir\\Rules\\Codes\\' . ucfirst(config('idir.payment.code_transfer.driver')) . '\\Transfer')
            ] : []
        ];
    }
}
