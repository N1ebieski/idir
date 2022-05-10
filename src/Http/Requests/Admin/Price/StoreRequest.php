<?php

namespace N1ebieski\IDir\Http\Requests\Admin\Price;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\IDir\ValueObjects\Price\Type;
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
}
