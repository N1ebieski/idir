<?php

namespace N1ebieski\IDir\Http\Requests\Api\Dir;

use Illuminate\Foundation\Http\FormRequest;

class DestroyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return optional($this->user())->can('admin.dirs.delete');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return optional($this->user())->can('admin.dirs.delete') ? [
            'reason' => 'nullable|string'
        ] : [];
    }
}
