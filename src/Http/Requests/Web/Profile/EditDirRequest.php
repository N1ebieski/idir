<?php

namespace N1ebieski\IDir\Http\Requests\Web\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;
use N1ebieski\IDir\Models\Group;

class EditDirRequest extends FormRequest
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
        $paginate = Config::get('database.paginate');

        return [
            'page' => 'integer',
            'except' => 'filled|array',
            'except.*' => 'integer',
            'filter.search' => 'bail|nullable|string|min:3|max:255',
            'filter.status' => 'bail|nullable|integer|between:0,4|no_js_validation',
            'filter.group' => [
                'bail',
                'nullable',
                'integer',
                'no_js_validation',
                Rule::exists('groups', 'id')->where('visible', Group::VISIBLE)
            ],
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
