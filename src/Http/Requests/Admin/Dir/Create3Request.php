<?php

namespace N1ebieski\IDir\Http\Requests\Admin\Dir;

use N1ebieski\IDir\Http\Requests\Admin\Dir\Store2Request;
use N1ebieski\IDir\Models\BanValue;

/**
 * [Create3Request description]
 */
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
        return $this->group->isAvailable();
    }

    /**
     * Get the URL to redirect to on a validation error.
     *
     * @return string
     */
    protected function getRedirectUrl()
    {
        $url = $this->redirector->getUrlGenerator();

        return $url->route('admin.dir.create_2', [$this->group->id]);
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation() : void
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
