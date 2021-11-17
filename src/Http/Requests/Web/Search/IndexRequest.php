<?php

namespace N1ebieski\IDir\Http\Requests\Web\Search;

use N1ebieski\ICore\Http\Requests\Web\Search\IndexRequest as BaseIndexRequest;

class IndexRequest extends BaseIndexRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            'source' => 'required|in:post,dir'
        ]);
    }
}
