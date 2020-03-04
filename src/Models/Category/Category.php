<?php
namespace N1ebieski\IDir\Models\Category;

use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Models\Category\Category as BaseCategory;
use N1ebieski\IDir\Repositories\CategoryRepo;
use N1ebieski\IDir\Cache\CategoryCache;

/**
 * [Category description]
 */
class Category extends BaseCategory
{
    // Makers

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
