<?php

namespace N1ebieski\IDir\Http\Requests\Web\Dir;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * [UpdateRenewRequest description]
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
        return $this->dir->group->isPublic() && $this->dir->isRenew();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'payment_type' => 'bail|required|string|in:transfer,code_sms,code_transfer|no_js_validation',
            'payment_transfer' => $this->input('payment_type') === 'transfer' ?
            [
                'bail',
                'required_if:payment_type,transfer',
                'integer',
                Rule::exists('prices', 'id')->where(function ($query) {
                    $query->where([
                        ['type', 'transfer'],
                        ['group_id', $this->dir->group->id]
                    ]);
                })
            ] : ['no_js_validation'],
            'payment_code_sms' => $this->input('payment_type') === 'code_sms' ?
             [
                'bail',
                'required_if:payment_type,code_sms',
                'integer',
                Rule::exists('prices', 'id')->where(function ($query) {
                    $query->where([
                        ['type', 'code_sms'],
                        ['group_id', $this->dir->group->id]
                    ]);
                })
            ] : ['no_js_validation'],
            'payment_code_transfer' => $this->input('payment_type') === 'code_transfer' ?
            [
                'bail',
                'required_if:payment_type,code_transfer',
                'integer',
                Rule::exists('prices', 'id')->where(function ($query) {
                    $query->where([
                        ['type', 'code_transfer'],
                        ['group_id', $this->dir->group->id]
                    ]);
                })
            ] : ['no_js_validation']
        ];
    }
}
