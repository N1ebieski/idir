<?php

namespace N1ebieski\IDir\Models\Category;

use Illuminate\Support\Facades\App;
use N1ebieski\IDir\Cache\Category\CategoryCache;
use N1ebieski\IDir\Repositories\Category\CategoryRepo;
use N1ebieski\ICore\Models\Category\Category as BaseCategory;

class Category extends BaseCategory
{
    // Factories

    /**
     * [makeRepo description]
     * @return CategoryRepo [description]
     */
    public function makeRepo()
    {
        return App::make(CategoryRepo::class, ['category' => $this]);
    }

    /**
     * [makeCache description]
     * @return CategoryCache [description]
     */
    public function makeCache()
    {
        return App::make(CategoryCache::class, ['category' => $this]);
    }
}
