<?php

namespace N1ebieski\IDir\Models;

use N1ebieski\ICore\Models\Link as BaseLink;
use N1ebieski\IDir\Cache\LinkCache;
use N1ebieski\IDir\Repositories\LinkRepo;

/**
 * [Link description]
 */
class Link extends BaseLink
{
    // Configuration

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return BaseLink::class;
    }

    // Makers

    /**
     * [makeCache description]
     * @return LinkCache [description]
     */
    public function makeCache()
    {
        return app()->make(LinkCache::class, ['link' => $this]);
    }

     /**
     * [makeRepo description]
     * @return LinkRepo [description]
     */
    public function makeRepo()
    {
        return app()->make(LinkRepo::class, ['link' => $this]);
    }   
}
