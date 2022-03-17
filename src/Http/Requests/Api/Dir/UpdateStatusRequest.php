<?php

namespace N1ebieski\IDir\Http\Requests\Api\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property Dir $dir
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
            'status' => [
                'bail',
                'required',
                'integer',
                Rule::in([
                    Dir::ACTIVE,
                    Dir::INACTIVE,
                    Dir::INCORRECT_INACTIVE
                ])
            ],
            'reason' => 'bail|nullable|string'
        ];
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function bodyParameters(): array
    {
        return [
            'status' => [
                'example' => Dir::ACTIVE
            ]
        ];
    }
}
