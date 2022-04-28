<?php

namespace N1ebieski\IDir\Http\Requests\Admin\DirBacklink;

use Illuminate\Foundation\Http\FormRequest;

class DelayRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->dirBacklink->dir->status->isBacklinkInactive();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'delay' => 'bail|required|int|min:1'
        ];
    }
}
