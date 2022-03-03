<?php

namespace N1ebieski\IDir\Http\Requests\Api\Group;

use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use N1ebieski\IDir\Models\Group;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Http\FormRequest;

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
        $paginate = Config::get('database.paginate');

        return [
            'page' => 'integer',
            'filter.except' => 'bail|nullable|array',
            'filter.except.*' => 'bail|integer',
            'filter.search' => 'bail|nullable|string|min:3|max:255',
            'filter.visible' => [
                'bail',
                'nullable',
                'integer',
                'in:1' . (
                    optional($this->user())->can('admin.groups.view') ? ',0' : null
                )
            ],
            'filter.orderby' => [
                'bail',
                'nullable',
                'in:created_at|asc,created_at|desc,updated_at|asc,updated_at|desc,name|asc,name|desc,position|asc,position|desc'
            ],
            'filter.paginate' => Rule::in([$paginate, ($paginate * 2), ($paginate * 4)]) . '|integer'
        ];
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function bodyParameters(): array
    {
        $paginate = Config::get('database.paginate');

        return [
            'page' => [
                'example' => 1
            ],
            'filter.except.*' => [
                'description' => 'Array containing IDs, excluding records from the list.',
                'example' => []
            ],
            'filter.search' => [
                'description' => 'Search by keyword.',
                'example' => ''
            ],
            'filter.visible' => [
                'description' => sprintf(
                    'Must be one of %1$s or %2$s (available only for admin.groups.view)',
                    Group::VISIBLE,
                    Group::INVISIBLE
                ),
                'example' => Group::VISIBLE
            ],
            'filter.orderby' => [
                'description' => 'Sorting the result list.',
                'example' => ''
            ],
            'filter.paginate' => [
                'description' => 'Number of records in the list.',
                'example' => Arr::random([$paginate, ($paginate * 2), ($paginate * 4)])
            ]
        ];
    }
}
