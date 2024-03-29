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
                'regex:/^(https|http):\/\/([\da-z\.-]+)(\.[a-zA-Z\d-]{2,})/'
            ]
        ];
    }
}
