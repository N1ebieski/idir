<?php

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
     * @param Group $group
     * @param Lang $lang
     * @param URL $url
     * @param Str $str
     */
    public function __construct(
        Group $group,
        Lang $lang,
        URL $url,
        Str $str
    ) {
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
    public function toArray(Collection $collection): array
    {
        $data = [];

        $groups = $this->group->all()
            ->map(function ($group, $key) {
                $group->color = $this->colors[$key] ?? $this->str->randomColor($group->id);

                return $group;
            });

        $collection->sortBy('group_id')
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
