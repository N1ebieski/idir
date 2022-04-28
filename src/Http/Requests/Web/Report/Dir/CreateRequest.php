<?php

namespace N1ebieski\IDir\Http\Requests\Web\Report\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property Dir $dir
 */
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
            //
        ];
    }
}
