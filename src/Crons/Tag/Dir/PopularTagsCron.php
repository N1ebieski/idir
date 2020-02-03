<?php

namespace N1ebieski\IDir\Crons\Tag\Dir;

use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\IDir\Jobs\Tag\Dir\CachePopularTags;
use N1ebieski\ICore\Crons\Tag\PopularTagsCron as BasePopularTagsCron;

/**
 * [PopularTagsCron description]
 */
class PopularTagsCron extends BasePopularTagsCron
{
    /**
     * Undocumented function
     *
     * @param Category $category
     * @param CachePopularTag $cachePopularTag
     */
    public function __construct(Category $category, CachePopularTags $cachePopularTags)
    {
        parent::__construct($category, $cachePopularTags);
    }
}
