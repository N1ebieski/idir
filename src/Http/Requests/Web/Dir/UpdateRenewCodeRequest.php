<?php

namespace N1ebieski\IDir\Http\Requests\Web\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\IDir\Http\Requests\Traits\HasCodes;

/**
 * @property Dir $dir
 */
class UpdateRenewCodeRequest extends FormRequest
{
    use HasCodes;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->dir->group->visible->isActive() && $this->dir->isRenew();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->prepareCodeRules();
    }
}
