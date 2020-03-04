<?php

namespace N1ebieski\IDir\Http\Requests\Admin\Category\Dir;

use N1ebieski\ICore\Http\Requests\Admin\Category\StoreGlobalRequest as BaseStoreGlobalRequest;
use N1ebieski\IDir\Models\Category\Dir\Category;
use Illuminate\Validation\Rule;

/**
 * [StoreGlobalRequest description]
 */
class StoreGlobalRequest extends BaseStoreGlobalRequest
{
    /**
     * [protected description]
     * @var Category
     */
    protected $category;

    /**
     * @param Category $category
     */
    public function __construct(Category $category)
    {
        parent::__construct();

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
            'parent_id' => [
                'nullable',
                Rule::exists('categories', 'id')->where(function ($query) {
                    $query->where('model_type', $this->category->model_type);
                })
            ]
        ]);
    }
}
