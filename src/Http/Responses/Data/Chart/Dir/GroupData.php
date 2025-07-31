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

namespace N1ebieski\IDir\Http\Responses\Data\Chart\Dir;

use Illuminate\Support\Str;
use N1ebieski\IDir\Models\Group;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Routing\UrlGenerator as URL;
use Illuminate\Contracts\Translation\Translator as Lang;
use N1ebieski\ICore\Http\Responses\Data\Chart\DataInterface;

class GroupData implements DataInterface
{
    /**
     * Undocumented function
     *
     * @var array
     */
    protected $colors = [
        'rgb(255, 193, 7)',
        'rgb(40, 167, 69)',
        'rgb(23, 162, 184)',
        'rgb(108, 117, 125)',
        'rgb(220, 53, 69)',
        'rgb(0, 123, 255)'
    ];

    /**
     * Undocumented function
     *
     * @param Group $group
     * @param Lang $lang
     * @param URL $url
     * @param Str $str
     */
    public function __construct(
        protected Group $group,
        protected Lang $lang,
        protected URL $url,
        protected Str $str
    ) {
        //
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function toArray(Collection $collection): array
    {
        $data = [];

        $groups = $this->group->all()
            ->map(function (Group $group, int $key) {
                $group->color = $this->colors[$key] ?? $this->str->randomColor((string)$group->id);

                return $group;
            });

        $collection->sortBy('group_id')
            ->each(function ($item) use (&$data, $groups) {
                /** @var object{group_id: int, count: int} $item */
                /** @var Group $group */
                $group = $groups->firstWhere('id', $item->group_id);

                $data[] = [
                    'group' => [
                        'id' => $group->id,
                        'name' => $group->name,
                    ],
                    'count' => $item->count,
                    'color' => $group->color,
                    'links' => [
                        'admin' => $this->url->route('admin.dir.index', ['filter[group]' => $item->group_id])
                    ]
                ];
            });

        return $data;
    }
}
