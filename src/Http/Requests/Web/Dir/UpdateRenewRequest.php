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

namespace N1ebieski\IDir\Http\Requests\Web\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\IDir\ValueObjects\Price\Type;
use Illuminate\Support\Collection as Collect;

/**
 * @property Dir $dir
 */
class UpdateRenewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->dir->group->visible->isActive() && $this->dir->isRenew();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'payment_type' => [
                'bail',
                'required',
                'string',
                Rule::in(Type::getAvailable()),
                'no_js_validation'
            ],
            'payment_transfer' => $this->input('payment_type') === Type::TRANSFER ?
            [
                'bail',
                'required_if:payment_type,' . Type::TRANSFER,
                'integer',
                Rule::exists('prices', 'id')->where(function ($query) {
                    $query->where([
                        ['type', Type::TRANSFER],
                        ['group_id', $this->dir->group->id]
                    ]);
                })
            ] : ['no_js_validation'],
            'payment_code_sms' => $this->input('payment_type') === Type::CODE_SMS ?
             [
                'bail',
                'required_if:payment_type,' . Type::CODE_SMS,
                'integer',
                Rule::exists('prices', 'id')->where(function ($query) {
                    $query->where([
                        ['type', Type::CODE_SMS],
                        ['group_id', $this->dir->group->id]
                    ]);
                })
            ] : ['no_js_validation'],
            'payment_code_transfer' => $this->input('payment_type') === Type::CODE_TRANSFER ?
            [
                'bail',
                'required_if:payment_type,' . Type::CODE_TRANSFER,
                'integer',
                Rule::exists('prices', 'id')->where(function ($query) {
                    $query->where([
                        ['type', Type::CODE_TRANSFER],
                        ['group_id', $this->dir->group->id]
                    ]);
                })
            ] : ['no_js_validation'],
            'payment_paypal_express' => $this->input('payment_type') === Type::PAYPAL_EXPRESS ?
            [
                'bail',
                'required_if:payment_type,' . Type::PAYPAL_EXPRESS,
                'integer',
                Rule::exists('prices', 'id')->where(function ($query) {
                    $query->where([
                        ['type', Type::PAYPAL_EXPRESS],
                        ['group_id', $this->dir->group->id]
                    ]);
                })
            ] : ['no_js_validation']
        ];
    }

    /**
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function validated($key = null, $default = null)
    {
        if (is_null($key)) {
            return Collect::make([
                'price' => $this->input("payment_{$this->input('payment_type')}")
            ])
            ->toArray();
        }

        return parent::validated($key, $default);
    }
}
