<?php

namespace N1ebieski\IDir\Http\Requests\Api\Category\Dir;

use Illuminate\Validation\Rule;
use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\ICore\Http\Requests\Api\Category\IndexRequest as BaseIndexRequest;

class IndexRequest extends BaseIndexRequest
{
    /**
     * Undocumented function
     *
     * @param Category $category
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            'filter.parent' => [
                'bail',
                'nullable',
                'integer',
                Rule::exists('categories', 'id')
                    ->where(function ($query) {
                        $query->where('model_type', $this->category->model_type)
                            ->when(
                                !optional($this->user())->can('admin.categories.view'),
                                function ($query) {
                                    $query->where('status', Category::ACTIVE);
                                }
                            );
                    })
            ]
        ]);
    }
}
