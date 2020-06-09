<?php

namespace N1ebieski\IDir\Http\Requests\Admin\Dir;

use Illuminate\Foundation\Http\FormRequest;

/**
 * [DestroyRequest description]
 */
class DestroyRequest extends FormRequest
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
            'reason' => 'nullable|string'
        ];
    }
}
