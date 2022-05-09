<?php

namespace N1ebieski\IDir\Models;

use Illuminate\Support\Facades\App;
use N1ebieski\IDir\Cache\Link\LinkCache;
use N1ebieski\ICore\Models\Link as BaseLink;
use N1ebieski\IDir\Repositories\Link\LinkRepo;
use N1ebieski\IDir\Database\Factories\Link\LinkFactory;

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

    /**
     * Create a new factory instance for the model.
     *
     * @return LinkFactory
     */
    protected static function newFactory()
    {
        return \N1ebieski\IDir\Database\Factories\Link\LinkFactory::new();
    }

    // Factories

    /**
     * [makeCache description]
     * @return LinkCache [description]
     */
    public function makeCache()
    {
        return App::make(LinkCache::class, ['link' => $this]);
    }

     /**
     * [makeRepo description]
     * @return LinkRepo [description]
     */
    public function makeRepo()
    {
        return App::make(LinkRepo::class, ['link' => $this]);
    }

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return LinkFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
