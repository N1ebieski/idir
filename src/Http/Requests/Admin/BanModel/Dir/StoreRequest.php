<?php

namespace N1ebieski\IDir\Http\Requests\Admin\BanModel\Dir;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * [StoreRequest description]
 */
class StoreRequest extends FormRequest
{
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user' => [
                'nullable',
                'integer',
                'required_without_all:ip,url',
                'exists:users,id',
                Rule::unique('bans_models', 'model_id')->where(function($query) {
                    $query->where('model_type', 'N1ebieski\ICore\Models\User');
                })
            ],
            'ip' => [
                'nullable',
                'string',
                'required_without_all:user,url',
                Rule::unique('bans_values', 'value')->where(function($query) {
                    $query->where('type', 'ip');
                })
            ],
            'url' => [
                'nullable',
                'string',
                'required_without_all:user,ip',
                Rule::unique('bans_values', 'value')->where(function($query) {
                    $query->where('type', 'url');
                })
            ]
        ];
    }
}
