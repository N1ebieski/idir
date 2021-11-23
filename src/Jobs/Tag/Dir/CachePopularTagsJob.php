<?php

namespace N1ebieski\IDir\Jobs\Tag\Dir;

use N1ebieski\ICore\Jobs\Tag\CachePopularTagsJob as BaseCachePopularTagsJob;

class CachePopularTagsJob extends BaseCachePopularTagsJob
{
    /**
     * Create a new job instance.
     *
     * @param array|null $cats
     * @return void
     */
    public function __construct(array $cats = null)
    {
        parent::__construct($cats);
    }
}
