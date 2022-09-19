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

namespace N1ebieski\IDir\Http\Requests\Admin\Price;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\IDir\ValueObjects\Price\Type;
use Illuminate\Support\Collection as Collect;
use N1ebieski\IDir\Http\Requests\Admin\Price\Traits\HasCodes;

class StoreRequest extends FormRequest
{
    use HasCodes;

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
     * [prepareForValidation description]
     */
    public function prepareForValidation(): void
    {
        $this->prepareCodesAttribute();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function prepareCodesAttribute(): void
    {
        $type = $this->input('type');

        if (
            !in_array($type, [Type::CODE_SMS, Type::CODE_TRANSFER])
            || empty($this->input("{$type}.codes.codes"))
        ) {
            return;
        }

        $this->merge([
            $type => [
                'codes' => [
                    'codes' => $this->prepareCodes($this->input("{$type}.codes.codes"))
                ] + $this->input("{$type}.codes")
            ] + $this->input($type)
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'price' => 'bail|numeric|between:0,9999.99',
            'days' => 'bail|nullable|integer',
            'type' => [
                'bail',
                Rule::in(Type::getAvailable())
            ],
            'code_sms.code' => $this->input('type') === Type::CODE_SMS ? [
                'bail',
                'required_if:type,' . Type::CODE_SMS,
                'string'
            ] : [],
            'code_sms.token' => $this->input('type') === Type::CODE_SMS ? [
                'bail',
                'nullable',
                'string'
            ] : [],
            'code_sms.number' => $this->input('type') === Type::CODE_SMS ? [
                'bail',
                'required_if:type,' . Type::CODE_SMS,
                'integer'
            ] : [],
            'code_sms.codes.codes' => $this->input('type') === Type::CODE_SMS ? [
                'bail',
                'nullable',
                'array'
            ] : [],
            'code_sms.codes.sync' => $this->input('type') === Type::CODE_SMS ? [
                'bail',
                'nullable'
            ] : [],
            'code_transfer.code' => $this->input('type') === Type::CODE_TRANSFER ? [
                'bail',
                'required_if:type,' . Type::CODE_TRANSFER,
                'string'
            ] : [],
            'code_transfer.codes.codes' => $this->input('type') === Type::CODE_TRANSFER ? [
                'bail',
                'nullable',
                'array'
            ] : [],
            'code_transfer.codes.sync' => $this->input('type') === Type::CODE_TRANSFER ? [
                'bail',
                'nullable'
            ] : [],
            'group' => 'bail|required|integer|exists:groups,id'
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
            return Collect::make($this->safe()->except(Type::getAvailable()))
                ->merge($this->safe()->collect()->get(optional($this->safe())->type, []))
                ->toArray();
        }

        return parent::validated($key, $default);
    }
}
