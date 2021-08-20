<?php

namespace N1ebieski\IDir\Http\Requests\Admin\Role;

use N1ebieski\ICore\Http\Requests\Admin\Role\UpdateRequest as BaseUpdateRequest;

/**
 * Klasa do usunięcia
 */
class UpdateRequest extends BaseUpdateRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return parent::rules()
    }
}
