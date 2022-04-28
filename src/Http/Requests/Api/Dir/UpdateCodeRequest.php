<?php

namespace N1ebieski\IDir\Http\Requests\Api\Dir;

use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Group;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\IDir\Http\Requests\Traits\CodePayable;

/**
 * @property Dir $dir
 * @property Group $group
 */
class UpdateCodeRequest extends FormRequest
{
    use CodePayable;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $check = $this->group && (
            $this->group->isPublic() || optional($this->user())->can('admin.dirs.edit')
        );

        return $this->group->id === $this->dir->group->id ?
            $check : $check && $this->group->isAvailable();
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
