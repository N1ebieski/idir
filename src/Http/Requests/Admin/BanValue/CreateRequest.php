<?php

namespace N1ebieski\IDir\Http\Requests\Admin\BanValue;

use N1ebieski\ICore\Http\Requests\Admin\BanValue\CreateRequest as BaseCreateRequest;

/**
 * [CreateRequest description]
 */
class CreateRequest extends BaseCreateRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            'type' => 'required|string|in:ip,word,url'
        ]);
    }
}
