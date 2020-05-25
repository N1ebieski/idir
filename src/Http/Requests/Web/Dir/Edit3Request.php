<?php

namespace N1ebieski\IDir\Http\Requests\Web\Dir;

use N1ebieski\ICore\Models\BanValue;
use N1ebieski\IDir\Http\Requests\Web\Dir\Update2Request;

/**
 * [Edit3Request description]
 */
class Edit3Request extends Update2Request
{
    /**
     * [__construct description]
     * @param BanValue $banValue [description]
     */
    public function __construct(BanValue $banValue)
    {
        parent::__construct($banValue);
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $check = $this->group->isPublic();

        return $this->dir->isGroup($this->group->id) ?
            $check :
            ($check && $this->group->isAvailable());
    }

    /**
     * Get the URL to redirect to on a validation error.
     *
     * @return string
     */
    protected function getRedirectUrl()
    {
        $url = $this->redirector->getUrlGenerator();

        return $url->route('web.dir.edit_2', [$this->dir->id, $this->group->id]);
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation() : void
    {
        if ($this->session()->has("dirId.{$this->dir->id}")) {
            $this->merge($this->session()->get("dirId.{$this->dir->id}"));
        }

        parent::prepareForValidation();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return parent::rules();
    }
}
