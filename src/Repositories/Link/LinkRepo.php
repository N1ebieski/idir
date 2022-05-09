<?php

namespace N1ebieski\IDir\Repositories\Link;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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
        return $this->link->where('type', 'link')
            ->when($component['home'] === true, function ($query) {
                $query->whereDoesntHave('categories')
                    ->when($this->migrationUtil->contains('add_home_to_links_table'), function ($query) {
                        $query->orWhere('home', true);
                    });
            }, function ($query) {
                $query->where(function ($query) {
                    $query->whereDoesntHave('categories')
                        ->when($this->migrationUtil->contains('add_home_to_links_table'), function ($query) {
                            $query->where('home', false);
                        });
                });
            })
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
