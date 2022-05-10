<?php

namespace N1ebieski\IDir\Http\Requests\Traits;

use Illuminate\Support\Facades\App;
use N1ebieski\IDir\ValueObjects\Price\Type;

trait HasCodes
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function prepareCodeRules()
    {
        return [
            'code_sms' => $this->input('payment_type') === Type::CODE_SMS ?
                [
                    'bail',
                    'nullable',
                    'required_if:payment_type,' . Type::CODE_SMS,
                    'string',
                    App::make(\N1ebieski\IDir\Rules\Codes\SMSRule::class)
                ]
                : [],
            'code_transfer' => $this->input('payment_type') === Type::CODE_TRANSFER ?
                [
                    'bail',
                    'nullable',
                    'required_if:payment_type,' . Type::CODE_TRANSFER,
                    'string',
                    App::make(\N1ebieski\IDir\Rules\Codes\TransferRule::class)
                ]
                : []
        ];
    }
}
