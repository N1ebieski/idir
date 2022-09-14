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

use Illuminate\Http\Request;
use N1ebieski\IDir\Models\Code;
use N1ebieski\IDir\Models\Price;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Translation\Translator as Lang;

abstract class CodesRule implements Rule
{
    /**
     * Undocumented function
     *
     * @param Request $request
     * @param Lang $lang
     */
    public function __construct(
        protected Price $price,
        protected Request $request,
        protected Lang $lang
    ) {
        //
    }

    /**
     *
     * @param mixed $attribute
     * @param mixed $value
     * @param mixed $parameters
     * @param mixed $validator
     * @return bool
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
        if (($check = $this->price->makeRepo()->firstCodeByCode($value)) instanceof Code) {
            if ($check->quantity === 1) {
                $check->delete();
            } elseif ($check->quantity > 1) {
                $check->decrement('quantity');
            }

            $this->request->merge([
                'logs' => ['code' => $value]
            ]);

            return true;
        }

        return false;
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
