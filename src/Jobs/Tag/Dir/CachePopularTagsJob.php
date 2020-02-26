<?php

namespace N1ebieski\IDir\Jobs\Tag\Dir;

use N1ebieski\ICore\Jobs\Tag\CachePopularTags as BaseCachePopularTags;

/**
 * [CachePopularTags description]
 */
class CachePopularTagsJob extends BaseCachePopularTags
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
