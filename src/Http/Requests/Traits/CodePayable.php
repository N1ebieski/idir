<?php

namespace N1ebieski\IDir\Http\Requests\Traits;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

/**
 * [trait description]
 */
trait CodePayable
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function prepareCodeRules()
    {
        return [
            'code_sms' => $this->input('payment_type') === 'code_sms' ?
                [
                    'bail',
                    'nullable',
                    'required_if:payment_type,code_sms',
                    'string',
                    App::make('N1ebieski\\IDir\\Rules\\Codes\\' . ucfirst(Config::get('idir.payment.code_sms.driver')) . '\\SMSRule')
                ]
                : [],
            'code_transfer' => $this->input('payment_type') === 'code_transfer' ?
                [
                    'bail',
                    'nullable',
                    'required_if:payment_type,code_transfer',
                    'string',
                    App::make('N1ebieski\\IDir\\Rules\\Codes\\' . ucfirst(Config::get('idir.payment.code_transfer.driver')) . '\\TransferRule')
                ]
                : []
        ];
    }
}
