<?php

namespace N1ebieski\IDir\Database\Factories\Category\Dir;

use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\ICore\Database\Factories\Category\CategoryFactory as BaseCategoryFactory;

class CategoryFactory extends BaseCategoryFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;
}
