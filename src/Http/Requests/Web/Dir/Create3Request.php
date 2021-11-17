<?php

namespace N1ebieski\IDir\Http\Requests\Web\Dir;

use N1ebieski\IDir\Models\BanValue;
use N1ebieski\IDir\Http\Requests\Web\Dir\Store2Request;

class Create3Request extends Store2Request
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
        return $this->group->isAvailable() && $this->group->isPublic();
    }

    /**
     * Get the URL to redirect to on a validation error.
     *
     * @return string
     */
    protected function getRedirectUrl()
    {
        $url = $this->redirector->getUrlGenerator();

        return $url->route('web.dir.create_2', [$this->group->id]);
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        if ($this->session()->has('dir')) {
            $this->merge($this->session()->get('dir'));
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
