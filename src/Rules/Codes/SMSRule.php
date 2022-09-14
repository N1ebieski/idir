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

namespace N1ebieski\IDir\Rules\Codes;

use LogicException;
use Illuminate\Http\Request;
use N1ebieski\IDir\Models\Price;
use N1ebieski\IDir\Rules\Codes\CodesRule;
use Illuminate\Contracts\Translation\Translator as Lang;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Codes\SmsClientInterface;

class SMSRule extends CodesRule
{
    /**
     * Undocumented function
     *
     * @param Price $price
     * @param Request $request
     * @param Lang $lang
     * @param SmsClientInterface $client
     */
    public function __construct(
        Price $price,
        protected Request $request,
        protected Lang $lang,
        protected SmsClientInterface $client
    ) {
        /** @var Price */
        $price = $price->findOrFail($this->request->input('payment_code_sms'));

        parent::__construct($price, $request, $lang);
    }

    /**
     *
     * @param mixed $attribute
     * @param mixed $value
     * @param mixed $parameters
     * @param mixed $validator
     * @return bool
     * @throws LogicException
     */
    public function validate($attribute, $value, $parameters, $validator)
    {
        return $this->passes($attribute, $value);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if (parent::passes($attribute, $value) === true) {
            return true;
        }

        if ($this->price->token === null) {
            return false;
        }

        try {
            $response = $this->client->authorize([
                'code' => $value,
                'number' => $this->price->number,
                'token' => $this->price->token
            ]);
        } catch (\Exception $e) {
            return false;
        }

        $this->request->merge([
            'logs' => array_merge((array)$response->getParameters(), ['code' => $value])
        ]);

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->lang->get('idir::validation.code');
    }
}
