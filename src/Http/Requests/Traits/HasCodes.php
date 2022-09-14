<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

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
