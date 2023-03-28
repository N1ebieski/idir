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

namespace N1ebieski\IDir\Http\Requests\Api\Payment\PayPal;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
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
        $this->merge([
            'uuid' => $this->input('invoice')
        ]);

        App::make(Request::class)->merge([
            'uuid' => $this->input('invoice'),
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
            'invoice' => 'bail|required|uuid',
            'uuid' => 'bail|required|uuid'
        ];
    }

    /**
     * We have to override method because PayPal IPN needs exactly the same raw
     * data to send back. Laravel's all() method returns converted data (for example
     * middleware replace empty strings to null)
     *
     * @param array|mixed|null $keys
     * @return array
     */
    public function all($keys = null)
    {
        $all = parent::all($keys);

        $raws = explode('&', $this->getContent());

        $data = [];

        foreach ($raws as $raw) {
            [$key, $value] = explode('=', $raw);

            $data[$key] = urldecode($value);
        }

        return array_replace_recursive($all, $data);
    }
}
