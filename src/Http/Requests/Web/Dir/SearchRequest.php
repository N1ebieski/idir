<?php

namespace N1ebieski\IDir\Http\Requests\Web\Dir;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
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
        return [
            'filter.orderby' => [
                'bail',
                'nullable',
                'in:created_at|asc,created_at|desc,updated_at|asc,updated_at|desc,title|asc,title|desc,sum_rating|desc,sum_rating|asc',
                'no_js_validation'
            ],
            'search' => 'required|string|min:3|max:255'
        ];
    }
}
