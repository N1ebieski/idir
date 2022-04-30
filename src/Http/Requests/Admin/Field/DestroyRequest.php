<?php

namespace N1ebieski\IDir\Http\Requests\Admin\Field;

use N1ebieski\IDir\Models\Field\Field;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property Field $field
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
        return !$this->field->type->isDefault();
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
