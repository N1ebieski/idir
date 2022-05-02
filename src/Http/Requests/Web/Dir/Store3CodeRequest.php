<?php

namespace N1ebieski\IDir\Http\Requests\Web\Dir;

use N1ebieski\IDir\Models\Group;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\IDir\Http\Requests\Traits\CodePayable;

/**
 * @property Group $group
 */
class Store3CodeRequest extends FormRequest
{
    use CodePayable;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->group->isAvailable() && $this->group->visible->isActive();
    }

    /**
     * Get the URL to redirect to on a validation error.
     *
     * @return string
     */
    protected function getRedirectUrl()
    {
        $url = $this->redirector->getUrlGenerator();

        return $url->route('web.dir.create_3', [$this->group->id]);
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
