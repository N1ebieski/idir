<?php

namespace N1ebieski\IDir\Http\Requests\Admin\Price;

use Illuminate\Validation\Rule;
use N1ebieski\IDir\Models\Price;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\IDir\Http\Requests\Admin\Price\Traits\CodePayable;

class UpdateRequest extends FormRequest
{
    use CodePayable;

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
    public function prepareForValidation() : void
    {
        $this->prepareCodesAttribute();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function prepareCodesAttribute() : void
    {
        $type = $this->input('type');

        if (!in_array($type, ['code_sms', 'code_transfer'])
        || empty($this->input("{$type}.codes.codes"))) {
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
                Rule::in(Price::AVAILABLE)
            ],
            'code_sms.code' => $this->input('type') === 'code_sms' ? [
                'bail',
                'required_if:type,code_sms',
                'string'
            ] : [],
            'code_sms.token' => $this->input('type') === 'code_sms' ? [
                'bail',
                'nullable',
                'string'
            ] : [],
            'code_sms.number' => $this->input('type') === 'code_sms' ? [
                'bail',
                'required_if:type,code_sms',
                'integer'
            ] : [],
            'code_sms.codes.codes' => $this->input('type') === 'code_sms' ? [
                'bail',
                'nullable',
                'array'
            ] : [],
            'code_sms.codes.sync' => $this->input('type') === 'code_sms' ? [
                'bail',
                'nullable'
            ] : [],
            'code_transfer.code' => $this->input('type') === 'code_transfer' ? [
                'bail',
                'required_if:type,code_transfer',
                'string'
            ] : [],
            'code_transfer.codes.codes' => $this->input('type') === 'code_transfer' ? [
                'bail',
                'nullable',
                'array'
            ] : [],
            'code_transfer.codes.sync' => $this->input('type') === 'code_transfer' ? [
                'bail',
                'nullable'
            ] : [],
            'group' => 'bail|required|integer|exists:groups,id'
        ];
    }
}
