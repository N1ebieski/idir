<?php

namespace N1ebieski\IDir\Http\Requests\Admin\Field\Group;

use N1ebieski\IDir\Http\Requests\Admin\Field\IndexRequest as BaseIndexRequest;

/**
 * [StoreRequest description]
 */
class IndexRequest extends BaseIndexRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge([
            'filter.morph' => 'bail|nullable|integer|exists:groups,id|no_js_validation'
        ], parent::rules());
    }
}
