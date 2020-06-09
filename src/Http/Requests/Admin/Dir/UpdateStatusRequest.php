<?php

namespace N1ebieski\IDir\Http\Requests\Admin\Dir;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'status' => [
                'required',
                'integer',
                Rule::in([
                    $this->dir::ACTIVE,
                    $this->dir::INACTIVE,
                    $this->dir::INCORRECT_INACTIVE
                ])
            ],
            'reason' => 'nullable|string'
        ];
    }
}
