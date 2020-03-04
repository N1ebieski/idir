<?php

namespace N1ebieski\IDir\Repositories;

use N1ebieski\ICore\Models\Link;
use N1ebieski\ICore\Repositories\LinkRepo as BaseLinkRepo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Config\Repository as Config;

/**
 * [LinkRepo description]
 */
class LinkRepo extends BaseLinkRepo
{
    /**
     * [__construct description]
     * @param Link   $link   [description]
     * @param Config $config [description]
     */
    public function __construct(Link $link, Config $config)
    {
        parent::__construct($link, $config);
    }

    /**
     * [getLinksUnionDirsByComponent description]
     * @param  Builder|null    $dirs      [description]
     * @param  array      $component [description]
     * @return Collection            [description]
     */
    public function getLinksUnionDirsByComponent(Builder $dirs = null, array $component) : Collection
    {
        return $this->link->where('type', 'link')
            ->whereDoesntHave('categories')
            ->when($component['cats'] !== null, function ($query) use ($component) {
                $query->orWhereHas('categories', function ($query) use ($component) {
                    $query->whereIn('id', $component['cats']);
                });
            })
            ->orderBy('position', 'asc')
            ->when($component['cats'] !== null, function ($query) use ($dirs) {
                $query->union($dirs);
            })
            ->limit($component['limit'])
            ->get(['id', 'url', 'name', 'img_url']);
    }
}
