<?php

namespace N1ebieski\IDir\Http\Requests\Admin\Comment\Dir;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\ICore\ValueObjects\Comment\Status;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->dir->status->isActive();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'parent_id' => [
                'required',
                'integer',
                Rule::exists('comments', 'id')->where(function ($query) {
                    $query->where('status', Status::ACTIVE);
                }),
            ]
        ];
    }
}
