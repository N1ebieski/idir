<?php

namespace N1ebieski\IDir\Http\Requests\Admin\Field;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'filter.search' => 'bail|nullable|string|min:3|max:255',
            'filter.visible' => 'bail|nullable|integer|in:0,1|no_js_validation',
            'filter.type' => 'bail|nullable|string|in:input,textarea,select,multiselect,checkbox,image|no_js_validation',
            'filter.orderby' => [
                'bail',
                'nullable',
                'in:created_at|asc,created_at|desc,updated_at|asc,updated_at|desc,title|asc,title|desc,position|asc,position|desc',
                'no_js_validation'
            ],
            'filter.paginate' => Rule::in([$paginate, ($paginate*2), ($paginate*4)]) . '|integer|no_js_validation'
        ];
    }
}
