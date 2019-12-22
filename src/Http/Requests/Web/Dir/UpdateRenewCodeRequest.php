<?php

namespace N1ebieski\IDir\Http\Requests\Web\Dir;

use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\IDir\Http\Requests\Traits\CodePayable;

/**
 * [UpdateRenewCodeRequest description]
 */
class UpdateRenewCodeRequest extends FormRequest
{
    use CodePayable;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->dir->group->isPublic() && $this->dir->isRenew();
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
