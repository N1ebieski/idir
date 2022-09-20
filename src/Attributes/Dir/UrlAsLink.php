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

namespace N1ebieski\IDir\Attributes\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use N1ebieski\ICore\Utils\MigrationUtil;
use N1ebieski\ICore\ValueObjects\Stat\Slug;
use Illuminate\Database\Eloquent\Casts\Attribute;

class UrlAsLink
{
    /**
     *
     * @param Dir $dir
     * @return void
     */
    public function __construct(protected Dir $dir)
    {
        //
    }

    /**
     *
     * @return Attribute
     */
    public function __invoke(): Attribute
    {
        return new Attribute(
            get: function (): ?string {
                if ($this->dir->url->isUrl()) {
                    $link = '<a rel="noopener';

                    if ($this->dir->getRelation('group')->hasNoFollowPrivilege()) {
                        $link .= ' nofollow';
                    }

                    $link .= '" target="_blank" title="' . e($this->dir->title) . '" ';

                    /** @var MigrationUtil */
                    $migrationUtil = App::make(MigrationUtil::class);

                    if ($migrationUtil->contains('create_stats_table')) {
                        $link .= 'class="click-stat" data-route="' . URL::route('web.stat.dir.click', [Slug::CLICK, $this->dir->slug]) . '" ';
                    }

                    $link .= 'href="' . e($this->dir->url) . '">' . e($this->dir->url->getHost()) . '</a>';
                }

                return $link ?? null;
            }
        );
    }
}
