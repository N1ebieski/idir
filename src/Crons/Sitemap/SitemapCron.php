<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

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
