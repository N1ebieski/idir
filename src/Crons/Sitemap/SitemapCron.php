<?php

namespace N1ebieski\IDir\Crons\Sitemap;

use N1ebieski\ICore\Crons\Sitemap\SitemapCron as BaseSitemapCron;

class SitemapCron extends BaseSitemapCron
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $builders = [
        \N1ebieski\IDir\Crons\Sitemap\Builder\DirBuilder::class,
        \N1ebieski\IDir\Crons\Sitemap\Builder\Category\Dir\CategoryBuilder::class,
        \N1ebieski\IDir\Crons\Sitemap\Builder\SitemapBuilder::class
    ];
}
