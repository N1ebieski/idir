<?php

namespace N1ebieski\IDir\Http\Requests\Admin\Dir;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * [IndexRequest description]
 */
class IndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $paginate = config('database.paginate');

        return [
            'page' => 'integer',
            'except' => 'filled|array',
            'except.*' => 'integer',
            'filter.search' => 'bail|nullable|string|min:3|max:255',
            'filter.status' => 'bail|nullable|integer|in:0,1,2,3|no_js_validation',
            'filter.group' => 'bail|nullable|integer|exists:groups,id|no_js_validation',
            'filter.category' => 'bail|nullable|integer|exists:categories,id|no_js_validation',
            'filter.author' => 'bail|nullable|integer|exists:users,id|no_js_validation',
            'filter.report' => 'bail|nullable|integer|in:0,1|no_js_validation',
            'filter.orderby' => [
                'bail',
                'nullable',
                'in:created_at|asc,created_at|desc,updated_at|asc,updated_at|desc,title|asc,title|desc,sum_rating|desc,sum_rating|asc',
                'no_js_validation'
            ],
            'filter.paginate' => Rule::in([$paginate, ($paginate*2), ($paginate*4)]) . '|integer|no_js_validation'
        ];
    }
}
