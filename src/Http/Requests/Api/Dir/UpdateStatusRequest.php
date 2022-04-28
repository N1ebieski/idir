<?php

namespace N1ebieski\IDir\Http\Requests\Api\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\IDir\ValueObjects\Dir\Status;

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
        return $this->dir->status->isUpdateStatus();
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
                    Status::ACTIVE,
                    Status::INACTIVE,
                    Status::INCORRECT_INACTIVE
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
                'example' => Status::ACTIVE
            ]
        ];
    }
}
