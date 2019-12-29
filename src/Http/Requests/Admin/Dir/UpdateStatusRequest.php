<?php

namespace N1ebieski\IDir\Http\Requests\Admin\Dir;

use Illuminate\Foundation\Http\FormRequest;

/**
 * [UpdateStatusRequest description]
 */
class UpdateStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->dir->isUpdateStatus();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => 'required|integer|in:0,1'
        ];
    }
}
