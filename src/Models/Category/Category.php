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

namespace N1ebieski\IDir\Models\Category;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\IDir\Cache\Category\CategoryCache;
use N1ebieski\IDir\Repositories\Category\CategoryRepo;
use N1ebieski\ICore\Models\Category\Category as BaseCategory;

class Category extends BaseCategory
{
    // Loads

    /**
     * [loadNestedWithMorphsCount description]
     * @param  array    $filter    [description]
     * @return self [description]
     */
    public function loadNestedWithMorphsCountByFilter(array $filter): self
    {
        return $this
            ->loadCount([
                'morphs' => function (Builder $query) {
                    return $query->active();
                }
            ])
            ->load([
                'childrens' => function (Builder|Category $query) {
                    return $query->active()
                        ->withCount([
                            'morphs' => function (Builder $query) {
                                return $query->active();
                            }
                        ])
                        ->orderBy('position', 'asc');
                },
                'ancestors' => function (Builder|Category $query) {
                    return $query->whereColumn('ancestor', '!=', 'descendant')
                        ->withCount([
                            'morphs' => function (Builder $query) {
                                return $query->active();
                            }
                        ])
                        ->orderBy('depth', 'desc');
                }
            ]);
    }

    // Factories

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
