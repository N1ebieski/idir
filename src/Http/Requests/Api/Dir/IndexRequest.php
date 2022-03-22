<?php

namespace N1ebieski\IDir\Http\Requests\Api\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Validation\Rule;
use N1ebieski\IDir\Models\Group;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\IDir\Models\Category\Dir\Category;

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

        return array_merge(
            [
                'page' => 'integer',
                'filter.except' => 'bail|nullable|array',
                'filter.except.*' => 'bail|integer',
                'filter.search' => 'bail|nullable|string|min:3|max:255',
                'filter.status' => [
                    'bail',
                    'nullable',
                    'integer',
                    Rule::in(array_merge(
                        [
                            Dir::ACTIVE
                        ],
                        optional($this->user())->can('api.dirs.view') ? [
                            Dir::INACTIVE,
                            Dir::PAYMENT_INACTIVE,
                            Dir::STATUS_INACTIVE,
                            Dir::BACKLINK_INACTIVE,
                            Dir::INCORRECT_INACTIVE
                        ] : [],
                    ))
                ],
                'filter.group' => [
                    'bail',
                    'nullable',
                    'integer',
                    Rule::exists('groups', 'id')->where(function ($query) {
                        $query->when(
                            !optional($this->user())->can('admin.dirs.view'),
                            function ($query) {
                                $query->where('visible', Group::VISIBLE);
                            }
                        );
                    })
                ],
                'filter.category' => [
                    'bail',
                    'nullable',
                    'integer',
                    Rule::exists('categories', 'id')->where(function ($query) {
                        $query->when(
                            !optional($this->user())->can('admin.dirs.view'),
                            function ($query) {
                                $query->where('status', Category::ACTIVE);
                            }
                        );
                    })
                ],
                'filter.orderby' => [
                    'bail',
                    'nullable',
                    'in:created_at|asc,created_at|desc,updated_at|asc,updated_at|desc,title|asc,title|desc,sum_rating|desc,sum_rating|asc,click|desc,click|asc,view|desc,view|asc',
                ],
                'filter.paginate' => [
                    'bail',
                    'nullable',
                    'integer',
                    Rule::in([$paginate, ($paginate * 2), ($paginate * 4)])
                ]
            ],
            optional($this->user())->can('admin.dirs.view') ?
            [
                'filter.author' => 'bail|nullable|integer|exists:users,id',
                'filter.report' => 'bail|nullable|integer|in:0,1',
            ] : []
        );
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function bodyParameters(): array
    {
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
            'filter.status' => [
                'description' => sprintf(
                    'Must be one of %1$s or (available only for admin.dirs.view) %2$s, %3$s, %4$s, %5$s, %6$s.',
                    Dir::ACTIVE,
                    Dir::INACTIVE,
                    Dir::PAYMENT_INACTIVE,
                    Dir::BACKLINK_INACTIVE,
                    Dir::STATUS_INACTIVE,
                    Dir::INCORRECT_INACTIVE
                ),
                'example' => Dir::ACTIVE
            ],
            'filter.group' => [
                'description' => 'ID of Group relationship.',
                'example' => ''
            ],
            'filter.category' => [
                'description' => 'ID of Category relationship.',
                'example' => ''
            ],
            'filter.orderby' => [
                'description' => 'Sorting the result list.',
                'example' => ''
            ],
            'filter.paginate' => [
                'description' => 'Number of records in the list.',
                'example' => ''
            ]
        ];
    }
}
