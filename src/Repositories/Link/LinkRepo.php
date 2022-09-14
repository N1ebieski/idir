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

namespace N1ebieski\IDir\Repositories\Link;

use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\ValueObjects\Link\Type;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Category\Category;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use N1ebieski\ICore\Repositories\Link\LinkRepo as BaseLinkRepo;

class LinkRepo extends BaseLinkRepo
{
    /**
     * [getLinksUnionDirsByComponent description]
     * @param  Builder|null    $dirs      [description]
     * @param  array      $component [description]
     * @return Collection            [description]
     */
    public function getLinksUnionDirsByComponent(Builder $dirs = null, array $component): Collection
    {
        return $this->link->newQuery()
            ->where('type', Type::LINK)
            ->when($component['home'] === true, function (Builder $query) {
                return $query->whereDoesntHave('categories')
                    ->when($this->migrationUtil->contains('add_home_to_links_table'), function (Builder $query) {
                        return $query->orWhere('home', true);
                    });
            }, function (Builder $query) {
                return $query->where(function (Builder $query) {
                    return $query->whereDoesntHave('categories')
                        ->when($this->migrationUtil->contains('add_home_to_links_table'), function (Builder $query) {
                            return $query->where('home', false);
                        });
                });
            })
            ->when(!is_null($component['cats']), function (Builder $query) use ($component) {
                return $query->orWhereHas('categories', function (MorphToMany|Builder|Category $query) use ($component) {
                    return $query->whereIn('id', $component['cats']);
                });
            })
            ->orderBy('position', 'asc')
            ->when(!is_null($component['cats']), function (Builder $query) use ($dirs) {
                $query->union($dirs->getQuery());
            })
            ->limit($component['limit'])
            ->get(['id', 'url', 'name', 'img_url']);
    }
}
