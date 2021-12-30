<?php

namespace N1ebieski\IDir\Http\Responses\Data\Dir\Chart;

use Illuminate\Support\Str;
use N1ebieski\IDir\Models\Group;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Routing\UrlGenerator as URL;
use N1ebieski\IDir\Http\Responses\Data\DataInterface;
use Illuminate\Contracts\Translation\Translator as Lang;

class GroupData implements DataInterface
{
    /**
     * Undocumented variable
     *
     * @var Collection
     */
    protected $collection;

    /**
     * Undocumented variable
     *
     * @var Group
     */
    protected $group;

    /**
     * Undocumented variable
     *
     * @var Lang
     */
    protected $lang;

    /**
     * Undocumented variable
     *
     * @var URL
     */
    protected $url;

    /**
     * Undocumented variable
     *
     * @var Str
     */
    protected $str;

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
     * @param Collection $collection
     * @param Group $group
     * @param Lang $lang
     * @param URL $url
     * @param Str $str
     */
    public function __construct(
        Collection $collection,
        Group $group,
        Lang $lang,
        URL $url,
        Str $str
    ) {
        $this->collection = $collection;

        $this->group = $group;

        $this->lang = $lang;
        $this->url = $url;
        $this->str = $str;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = [];

        $groups = $this->group->all()
            ->map(function ($group, $key) {
                $group->color = $this->colors[$key] ?? $this->str->randomColor($group->id);

                return $group;
            });

        $this->collection->sortBy('group_id')
            ->each(function ($item) use (&$data, $groups) {
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
