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

namespace N1ebieski\IDir\Http\Requests\Api\Payment\Cashbill;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use N1ebieski\IDir\Http\Requests\Api\Payment\Interfaces\VerifyRequestInterface;

class VerifyRequest extends FormRequest implements VerifyRequestInterface
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
     *
     * @return void
     * @throws BadRequestException
     */
    public function prepareForValidation(): void
    {
        if ($this->has('userdata')) {
            $userdata = json_decode($this->input('userdata'));

            $this->merge([
                'uuid' => $userdata->uuid,
                'redirect' => $userdata->redirect
            ]);
        }

        App::make(Request::class)->merge([
            'uuid' => $this->input('uuid'),
            'logs' => $this->all()
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'service' => 'bail|required|string|in:' . Config::get("services.cashbill.transfer.service"),
            'orderid' => 'bail|required|string',
            'amount' => 'bail|required|numeric|between:0,99999.99',
            'userdata' => 'bail|required|json',
            'status' => 'bail|required|in:ok,err',
            'sign' => 'bail|required|string',
            'uuid' => 'bail|required|uuid',
            'redirect' => [
                'bail',
                'nullable',
                'string',
                'regex:/^(https|http):\/\/([\da-z\.-]+)(\.[a-zA-Z\d-]{2,})/'
            ]
        ];
    }
}
